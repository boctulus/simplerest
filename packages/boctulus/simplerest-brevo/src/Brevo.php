<?php

namespace Boctulus\Simplerest\Core\Libs;

use Brevo\Brevo as BrevoClient;
use Brevo\Exceptions\BrevoApiException;
use Brevo\TransactionalEmails\Requests\SendTransacEmailRequest;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestSender;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestReplyTo;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestToItem;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestCcItem;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestBccItem;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestAttachmentItem;
use Boctulus\Simplerest\Mail\Interfaces\IMail;
use Boctulus\Simplerest\Mail\MailBase;

class Brevo extends MailBase implements IMail
{
    protected static ?string $messageId = null;

    public static function messageId(): ?string
    {
        return static::$messageId;
    }

    protected static function client(string $apiKey): BrevoClient
    {
        // Do not retry a send automatically: a timeout may follow an accepted email.
        return new BrevoClient($apiKey, [
            'client' => new \GuzzleHttp\Client([
                'http_errors' => false,
                'verify' => Config::get('email.ca_bundle') ?: true,
            ]),
            'timeout' => 30,
            'maxRetries' => 0,
        ]);
    }

    private static function recipients($addresses, string $type): array
    {
        if (is_string($addresses)) {
            $addresses = [['email' => $addresses]];
        } elseif (isset($addresses['email'])) {
            $addresses = [$addresses];
        }
        return array_map(static function ($address) use ($type) {
            $address = is_string($address) ? ['email' => $address] : $address;
            if (!filter_var($address['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Invalid recipient email');
            }
            return new $type($address);
        }, $addresses);
    }

    static function send($to, $subject = '', $body = '', $attachments = null, $from = [], array $cc = [], $bcc = [], $reply_to = [], $alt_body = null): bool
    {
        static::$errors = null;
        static::$messageId = null;
        $apiKey = '';

        try {
            if (!class_exists(BrevoClient::class)) {
                throw new \LogicException('Brevo requires getbrevo/brevo-php ^5.0 and guzzlehttp/guzzle.');
            }
            $config = Config::get();
            $apiKey = trim((string) ($config['brevo_api_key'] ?? $config['sendinblue_api_key'] ?? ''));
            if ($apiKey === '') {
                throw new \LogicException('Configure BREVO_API_KEY or SENDINBLUE_API_KEY for API sending; SMTP credentials are separate.');
            }
            $body = trim((string) $body);
            if (trim((string) $subject) === '') {
                throw new \InvalidArgumentException('Subject is required');
            }
            if ($body === '' && trim((string) $alt_body) === '') {
                throw new \InvalidArgumentException('Body or alt_body is required');
            }
            $from = is_string($from) ? ['email' => $from] : $from;
            $from['email'] ??= $config['email']['from']['address'] ?? null;
            $from['name'] ??= $config['email']['from']['name'] ?? '';
            if (!filter_var($from['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('A valid sender email is required');
            }
            $reply_to = $reply_to ?: $from;
            $reply_to = is_string($reply_to) ? ['email' => $reply_to] : $reply_to;
            if (!filter_var($reply_to['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Invalid reply-to email');
            }
            $args = [
                'sender' => new SendTransacEmailRequestSender($from),
                'replyTo' => new SendTransacEmailRequestReplyTo($reply_to),
                'to' => static::recipients($to, SendTransacEmailRequestToItem::class),
                'subject' => $subject,
            ];
            if (!$args['to']) {
                throw new \InvalidArgumentException('At least one recipient is required');
            }
            if ($body !== '') {
                $args['htmlContent'] = $body;
            }
            if ($alt_body !== null && $alt_body !== '') {
                $args['textContent'] = $alt_body;
            }
            if ($cc) {
                $args['cc'] = static::recipients($cc, SendTransacEmailRequestCcItem::class);
            }
            if ($bcc) {
                $args['bcc'] = static::recipients($bcc, SendTransacEmailRequestBccItem::class);
            }
            if ($attachments) {
                if (is_string($attachments) || isset($attachments['path']) || isset($attachments['data'])) {
                    $attachments = [$attachments];
                }
                foreach ($attachments as $attachment) {
                    $attachment = is_string($attachment) ? ['path' => $attachment] : $attachment;
                    if (isset($attachment['data'])) {
                        $data = $attachment['data'];
                        $name = $attachment['name'] ?? 'file.bin';
                    } else {
                        $path = $attachment['path'] ?? '';
                        if (!is_file($path) || !is_readable($path)) {
                            throw new \InvalidArgumentException('Attachment not found or unreadable');
                        }
                        $data = file_get_contents($path);
                        $name = $attachment['name'] ?? basename($path);
                    }
                    if (!is_string($data) || $data === '') {
                        throw new \InvalidArgumentException('Invalid attachment data');
                    }
                    $args['attachment'][] = new SendTransacEmailRequestAttachmentItem([
                        'name' => $name, 'content' => base64_encode($data),
                    ]);
                }
            }
            $result = static::client($apiKey)->transactionalEmails->sendTransacEmail(new SendTransacEmailRequest($args));
            if (empty($result?->messageId)) {
                throw new \RuntimeException('Brevo did not return a message ID');
            }
            static::$messageId = $result->messageId;
            return true;
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            if ($e instanceof BrevoApiException) {
                $response = $e->getBody();
                $detail = is_array($response) ? ($response['message'] ?? null) : (is_object($response) ? ($response->message ?? null) : null);
                $message = 'Brevo HTTP ' . $e->getCode() . ': ' . ($detail ?: 'API request failed');
            }
            static::$errors = $apiKey === '' ? $message : str_replace($apiKey, '[redacted]', $message);
            return false;
        }
    }
}

