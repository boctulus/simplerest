<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Api\AuthController;
use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Core\Libs\Mail;
use Boctulus\Simplerest\Models\main\UsersModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;
use Throwable;

/*
    Aquí puede usar los hooks disponibles
*/
class MyAuthController extends AuthController {

    private const RESET_PURPOSE = 'password_reset';
    private const MINIMUM_PASSWORD_LENGTH = 8;
    private const NEUTRAL_RESET_MESSAGE = 'If the account exists, a password reset link will be sent.';

    function onRegister(Array $data){ }
    function onRegistered(Array $data, $uid, $is_active, $roles){ }
    function onRemember(Array $data){}

    function onRemembered(Array $data, $link_url){
        // Envio el correo aca

        response([
            'message' => 'Correo enviado'
        ]);
    }
    
    function onLogin(Array $data){}
    function onLogged(Array $data, $uid, $is_active, $roles, $perms){}
    function onImpersonated(Array $data, $uid, $is_active, $roles, $perms, $impersonated_by){}	
    function onChecked($uid, $is_active, $roles, $perms, $auth_method){}
    function onConfirmedEmail($uid, $roles, $perms){}
    function onChangedPassword($uid, $roles, $perms){}

    function getDbAccess($uid) : Array { return []; }

    /*
        Password Reset — Secure override of rememberme()
    */
    function rememberme()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'OPTIONS'], true)) {
            error('Incorrect verb (' . $_SERVER['REQUEST_METHOD'] . '), expecting POST', 405);
        }

        $data = (array) request()->getBodyDecoded();
        $email = strtolower(trim((string) ($data[$this->__email] ?? '')));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error('A valid email is required', 422);
        }

        try {
            $model = new UsersModel(true);
            $user = $model->findPasswordResetCandidate($email);

            if ($user !== null) {
                [$token, $expiresAt] = $this->issuePasswordResetToken($user);

                $base = rtrim((string) $this->config['app_url'], '/');
                $url = $base . '/reset-password?token=' . rawurlencode($token)
                    . '&exp=' . $expiresAt;

                $this->deliverPasswordResetEmail($user, $url);
            }
        } catch (Throwable $exception) {
            $userId = isset($user['id']) ? (int) $user['id'] : 0;
            Logger::log(
                '[password-reset] delivery failed for user id '
                . $userId . ': ' . $exception->getMessage()
            );
        }

        response()->send([
            'message' => self::NEUTRAL_RESET_MESSAGE,
        ]);
    }

    /*
        Password Reset — validate token (GET)
    */
    function change_pass_by_link($jwt = null, $exp = null)
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'OPTIONS'], true)) {
            error('Incorrect verb (' . $_SERVER['REQUEST_METHOD'] . '), expecting GET', 405);
        }

        try {
            $payload = $this->validatePasswordResetToken((string) $jwt, $exp);

            response()->send([
                'valid' => true,
                'expires_at' => (int) $payload->exp,
            ]);
        } catch (Throwable $exception) {
            $this->sendResetError($exception);
        }
    }

    /*
        Password Reset — apply new password (POST)
    */
    function change_pass_process()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'OPTIONS'], true)) {
            error('Incorrect verb (' . $_SERVER['REQUEST_METHOD'] . '), expecting POST', 405);
        }

        $data = request()->getBody();
        $password = is_object($data) ? (string) ($data->password ?? '') : '';
        $confirmation = is_object($data) ? (string) ($data->password_confirmation ?? '') : '';

        if (strlen($password) < self::MINIMUM_PASSWORD_LENGTH) {
            error('Password must contain at least 8 characters', 422);
        }

        if ($confirmation !== '' && !hash_equals($password, $confirmation)) {
            error('Passwords do not match', 422);
        }

        try {
            $payload = $this->validatePasswordResetToken($this->extractBearerToken());

            $model = new UsersModel(true);
            $updated = $model->updatePasswordFromReset(
                (int) $payload->uid,
                $password
            );

            if (!$updated) {
                throw new RuntimeException('Could not update the password', 500);
            }

            $this->onChangedPassword((int) $payload->uid, [], []);

            response()->send([
                'message' => 'Password changed successfully.',
            ]);
        } catch (Throwable $exception) {
            $this->sendResetError($exception);
        }
    }

    /**
     * Issue a password-reset JWT that embeds a hash of the current password.
     * The hash binding makes the token single-use: once the password changes,
     * the embedded hash no longer matches and validation fails.
     *
     * @return array{0: string, 1: int}  [token, expiresAt]
     */
    protected function issuePasswordResetToken(array $user): array
    {
        $userId = (int) ($user[$this->__id] ?? $user['id'] ?? 0);
        $passwordHash = (string) ($user[$this->__password] ?? '');

        if ($userId < 1 || $passwordHash === '') {
            throw new RuntimeException('Invalid password reset candidate', 500);
        }

        $now = time();
        $expiresAt = $now + max(300, (int) $this->config['email_token']['expires_in']);
        $secret = $this->resetSecret();

        $payload = [
            'alg' => $this->config['email_token']['encryption'],
            'typ' => 'JWT',
            'iat' => $now,
            'nbf' => $now,
            'exp' => $expiresAt,
            'uid' => $userId,
            'purpose' => self::RESET_PURPOSE,
            'pwdv' => hash_hmac('sha256', $passwordHash, $secret),
        ];

        return [
            JWT::encode(
                $payload,
                $secret,
                $this->config['email_token']['encryption']
            ),
            $expiresAt,
        ];
    }

    /**
     * Validate a password-reset token: signature, expiry, purpose,
     * and password-version binding (single-use enforcement).
     */
    protected function validatePasswordResetToken(string $jwt, $expectedExpiration = null): object
    {
        if ($jwt === '') {
            throw new RuntimeException('The reset link is invalid or has expired', 400);
        }

        try {
            $payload = JWT::decode(
                $jwt,
                new Key(
                    $this->resetSecret(),
                    $this->config['email_token']['encryption']
                )
            );
        } catch (Throwable) {
            throw new RuntimeException('The reset link is invalid or has expired', 400);
        }

        if (
            ($payload->purpose ?? null) !== self::RESET_PURPOSE
            || empty($payload->uid)
            || empty($payload->pwdv)
        ) {
            throw new RuntimeException('The reset link is invalid or has expired', 400);
        }

        if (
            $expectedExpiration !== null
            && (int) $expectedExpiration !== (int) ($payload->exp ?? 0)
        ) {
            throw new RuntimeException('The reset link is invalid or has expired', 400);
        }

        $model = new UsersModel(true);
        $user = $model->findActiveForPasswordReset((int) $payload->uid);

        if ($user === null) {
            throw new RuntimeException('The reset link is invalid or has expired', 400);
        }

        $currentVersion = hash_hmac(
            'sha256',
            (string) $user[$this->__password],
            $this->resetSecret()
        );

        if (!hash_equals($currentVersion, (string) $payload->pwdv)) {
            throw new RuntimeException('The reset link is invalid or has expired', 400);
        }

        return $payload;
    }

    protected function deliverPasswordResetEmail(array $user, string $url): void
    {
        $name = htmlspecialchars((string) ($user['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

        $sent = Mail::send(
            [
                'email' => (string) $user[$this->__email],
                'name' => (string) ($user['name'] ?? ''),
            ],
            'Reset your password',
            '<p>Hi ' . $name . ',</p>'
            . '<p>We received a request to reset your password.</p>'
            . '<p><a href="' . $safeUrl . '">Reset my password</a></p>'
            . '<p>This link expires in one hour and can only be used once.</p>'
            . '<p>If you did not request this, you can ignore this email.</p>',
            null,
            [],
            [],
            [],
            [],
            'Use the password reset link. The link expires in one hour.'
        );

        if (!$sent) {
            throw new RuntimeException(
                'The password reset email could not be sent: ' . (string) Mail::errors()
            );
        }
    }

    private function extractBearerToken(): string
    {
        $headers = request()->headers();
        $authorization = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!preg_match('/^Bearer\s+(\S+)$/i', (string) $authorization, $matches)) {
            throw new RuntimeException('Authorization not found', 400);
        }

        return $matches[1];
    }

    private function resetSecret(): string
    {
        $secret = (string) ($this->config['email_token']['secret_key'] ?? '');
        $accessSecret = (string) ($this->config['access_token']['secret_key'] ?? '');

        if (strlen($secret) < 32 || hash_equals($accessSecret, $secret)) {
            throw new RuntimeException(
                'A dedicated JWT_EMAIL_SECRET of at least 32 characters is required',
                500
            );
        }

        return $secret;
    }

    private function sendResetError(Throwable $exception): void
    {
        $status = (int) $exception->getCode();
        if ($status < 400 || $status > 599) {
            $status = 500;
        }

        $message = $status >= 500
            ? 'Password reset could not be completed'
            : $exception->getMessage();

        error($message, $status);
    }
}
