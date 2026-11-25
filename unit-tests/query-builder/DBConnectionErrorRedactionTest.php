<?php

namespace Boctulus\Simplerest\tests;

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Config;

/*
    A failed connection must not put credentials in the exception.

    Uses sentinel values and a missing SQLite path: no database server needed.

    Run: ./vendor/bin/phpunit --no-configuration --bootstrap vendor/autoload.php unit-tests/query-builder/DBConnectionErrorRedactionTest.php
*/
class DBConnectionErrorRedactionTest extends TestCase
{
    private ?array $previousConfig = null;

    protected function setUp(): void
    {
        $this->previousConfig = $this->configData()->getValue();
    }

    protected function tearDown(): void
    {
        $this->configData()->setValue(null, $this->previousConfig);
    }

    private function configData(): \ReflectionProperty
    {
        $property = new \ReflectionProperty(Config::class, 'data');
        $property->setAccessible(true);

        return $property;
    }

    private function failedConnection(bool $debug): string
    {
        $this->configData()->setValue(null, [
            'db_connections' => [
                'redaction_probe' => [
                    'driver'      => 'sqlite',
                    'db_name'     => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'missing-' . bin2hex(random_bytes(6)) . DIRECTORY_SEPARATOR . 'x.db',
                    'user'        => 'SENTINEL_USER',
                    'pass'        => 'SENTINEL_PASS',
                    'pdo_options' => [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION],
                ],
            ],
            'db_connection_default' => 'redaction_probe',
            'debug'                 => $debug,
        ]);

        try {
            DB::getConnection('redaction_probe');
        } catch (\PDOException $e) {
            // Message, previous exception and trace: whatever a handler may print.
            return (string) $e;
        }

        $this->fail('The probe connection was expected to fail.');
    }

    public function testDebugErrorNamesTheConnectionWithoutCredentials(): void
    {
        $error = $this->failedConnection(true);

        $this->assertStringNotContainsString('SENTINEL_PASS', $error);
        $this->assertStringNotContainsString('SENTINEL_USER', $error);
        $this->assertStringContainsString('redaction_probe', $error);
        $this->assertStringContainsString('sqlite', $error);
    }

    public function testErrorWithoutDebugHasNoCredentials(): void
    {
        $error = $this->failedConnection(false);

        $this->assertStringNotContainsString('SENTINEL_PASS', $error);
        $this->assertStringNotContainsString('SENTINEL_USER', $error);
    }

    public static function serverMessages(): array
    {
        return [
            ["SQLSTATE[HY000] [1045] Access denied for user 'SENTINEL_USER'@'localhost' (using password: YES)"],
            ["SQLSTATE[HY000] [1044] Access denied for user 'SENTINEL_USER'@'%' to database 'x'"],
            ['SQLSTATE[08006] [7] FATAL:  password authentication failed for user "SENTINEL_USER"'],
            ['SQLSTATE[08006] [7] FATAL:  role "SENTINEL_USER" does not exist'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('serverMessages')]
    public function testUserNameIsRemovedFromServerMessages(string $message): void
    {
        $clean = DB::redactConnectionError($message);

        $this->assertStringNotContainsString('SENTINEL_USER', $clean);
        $this->assertStringContainsString('SQLSTATE', $clean);
    }

    public function testOtherMessagesAreKept(): void
    {
        $this->assertSame("Unknown database 'shop'", DB::redactConnectionError("Unknown database 'shop'"));
    }
}
