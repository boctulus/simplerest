<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\tests;

use Boctulus\Simplerest\Controllers\MyAuthController;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Models\main\UsersModel;
use PHPUnit\Framework\TestCase;
use RuntimeException;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

if (php_sapi_name() != "cli"){
    return;
}

require_once __DIR__ . '/../../app.php';

final class TestablePasswordResetController extends MyAuthController
{
    public function issueForTest(array $user): array
    {
        return $this->issuePasswordResetToken($user);
    }

    public function validateForTest(string $token, ?int $expiration = null): object
    {
        return $this->validatePasswordResetToken($token, $expiration);
    }
}

final class PasswordResetTest extends TestCase
{
    private \PDO $connection;
    private array $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = DB::getConnection();
        $this->user = (new UsersModel(true))
            ->assoc()
            ->unhide([UsersModel::$password])
            ->where([UsersModel::$is_active => 1])
            ->first();

        self::assertIsArray($this->user, 'An active user is required for password-reset tests.');
        self::assertNotEmpty($this->user[UsersModel::$password]);

        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->connection->inTransaction()) {
            $this->connection->rollBack();
        }

        parent::tearDown();
    }

    public function testPasswordUpdateHashesPlainTextAndDoesNotDoubleHash(): void
    {
        $originalHash = (string) $this->user[UsersModel::$password];
        $password = 'UnitResetPassword123!';

        self::assertTrue(
            (new UsersModel(true))->updatePasswordFromReset((int) $this->user['id'], $password)
        );

        $updated = (new UsersModel(true))->findActiveForPasswordReset((int) $this->user['id']);

        self::assertIsArray($updated);
        self::assertNotSame($password, $updated[UsersModel::$password]);
        self::assertTrue(password_verify($password, $updated[UsersModel::$password]));

        self::assertTrue(
            (new UsersModel(true))->updatePasswordFromReset(
                (int) $this->user['id'],
                $originalHash
            )
        );

        $restored = (new UsersModel(true))->findActiveForPasswordReset((int) $this->user['id']);
        self::assertSame($originalHash, $restored[UsersModel::$password]);
    }

    public function testResetTokenIsValidUntilPasswordChangesAndThenCannotBeReplayed(): void
    {
        $controller = new TestablePasswordResetController();
        [$token, $expiration] = $controller->issueForTest($this->user);

        $payload = $controller->validateForTest($token, $expiration);
        self::assertSame((int) $this->user['id'], (int) $payload->uid);

        self::assertTrue(
            (new UsersModel(true))->updatePasswordFromReset(
                (int) $this->user['id'],
                'AnotherUnitResetPassword123!'
            )
        );

        try {
            $controller->validateForTest($token, $expiration);
            self::fail('A reset token must be invalid after the password changes.');
        } catch (RuntimeException $exception) {
            self::assertSame(400, $exception->getCode());
        }
    }

    public function testResetTokenRejectsManipulatedExpiration(): void
    {
        $controller = new TestablePasswordResetController();
        [$token, $expiration] = $controller->issueForTest($this->user);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(400);

        $controller->validateForTest($token, $expiration + 1);
    }

    public function testResetSecretAndSmtpConfigurationAreComplete(): void
    {
        $config = Config::get();

        self::assertGreaterThanOrEqual(32, strlen((string) $config['email_token']['secret_key']));
        self::assertNotSame(
            $config['access_token']['secret_key'],
            $config['email_token']['secret_key']
        );

        $driver = (string) $config['email']['mailer_default'];
        self::assertArrayHasKey($driver, $config['email']['mailers']);

        foreach (['Host', 'Port', 'Username', 'Password', 'SMTPAuth', 'SMTPSecure'] as $field) {
            self::assertArrayHasKey($field, $config['email']['mailers'][$driver]);
            self::assertNotSame('', (string) $config['email']['mailers'][$driver][$field]);
        }
    }
}
