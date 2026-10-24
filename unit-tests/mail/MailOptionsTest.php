<?php

namespace Boctulus\Simplerest\tests\mail;

use Boctulus\Simplerest\Mail\Mail;
use Boctulus\Simplerest\Core\Libs\Env;
use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Framework\TestCase;

final class MailOptionsTestHarness extends Mail
{
    public static function configure(PHPMailer $mail, array $options): void
    {
        static::applySupportedOptions($mail, $options);
    }
}

class MailOptionsTest extends TestCase
{
    public function test_supported_phpmailer_options_are_applied(): void
    {
        $mail = new PHPMailer();

        MailOptionsTestHarness::configure($mail, [
            'Timeout'     => 10,
            'Debugoutput' => 'html',
        ]);

        $this->assertSame(10, $mail->Timeout);
        $this->assertSame('html', $mail->Debugoutput);
    }

    public function test_unknown_phpmailer_options_are_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unsupported PHPMailer option 'Debugutput'");

        MailOptionsTestHarness::configure(new PHPMailer(), [
            'Debugutput' => 'html',
        ]);
    }

    public function test_project_mailer_configuration_uses_supported_options(): void
    {
        $originalEnvData = Env::$data;

        try {
            Env::$data = [];
            $config = include dirname(__DIR__, 2) . '/config/config.php';
            $mail = new PHPMailer();

            MailOptionsTestHarness::configure($mail, $config['email']['mailers']['google']);

            $this->assertSame('html', $mail->Debugoutput);
        } finally {
            Env::$data = $originalEnvData;
        }
    }
}
