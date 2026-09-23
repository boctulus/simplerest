<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class ApiAuthorizationDispatchTest extends TestCase
{
    private function probe(string $originalMethod, ?string $overrideMethod, array $rolePermissions, ?int $tablePermissions): array
    {
        $fixture = __DIR__ . '/fixtures/api_authorization_dispatch_probe.php';
        $payload = base64_encode(json_encode([
            'original_method' => $originalMethod,
            'override_method' => $overrideMethod,
            'role_permissions' => $rolePermissions,
            'table_permissions' => $tablePermissions,
        ], JSON_THROW_ON_ERROR));

        $pipes = [];
        $process = proc_open(
            [PHP_BINARY, $fixture, $payload],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );

        $this->assertIsResource($process, 'Could not start the isolated API authorization probe');
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        $this->assertSame(0, $exitCode, "Probe failed. stdout: $stdout stderr: $stderr");

        return json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
    }

    public function test_user_post_bitmask_adds_get_instead_of_post(): void
    {
        $out = $this->probe('POST', null, [], 4);

        $this->assertSame('post', $out['dispatch_method']);
        $this->assertContains('get', $out['callables']);
        $this->assertNotContains('post', $out['callables']);
        $this->assertFalse($out['dispatch_allowed']);
    }

    public function test_user_patch_bitmask_adds_dispatched_patch_callable(): void
    {
        $out = $this->probe('PATCH', null, [], 2);

        $this->assertSame('patch', $out['dispatch_method']);
        $this->assertContains('patch', $out['callables']);
        $this->assertNotContains('putch', $out['callables']);
        $this->assertTrue($out['dispatch_allowed']);
    }

    public function test_user_zero_bitmask_does_not_remove_role_callable(): void
    {
        $out = $this->probe('POST', null, ['create' => true], 0);

        $this->assertContains('post', $out['callables']);
        $this->assertFalse(in_array('get', $out['callables'], true));
        $this->assertTrue($out['dispatch_allowed']);
    }

    public function test_header_override_dispatches_new_method_but_acl_uses_original_post(): void
    {
        $out = $this->probe('POST', 'PATCH', ['create' => true], null);

        $this->assertSame('POST', $out['original_method']);
        $this->assertSame('patch', $out['dispatch_method']);
        $this->assertContains('post', $out['callables']);
        $this->assertNotContains('patch', $out['callables']);
        $this->assertFalse($out['dispatch_allowed']);
    }

    public function test_header_override_from_patch_to_post_also_mismatches_acl_branch(): void
    {
        $out = $this->probe('PATCH', 'POST', ['update' => true], null);

        $this->assertSame('PATCH', $out['original_method']);
        $this->assertSame('post', $out['dispatch_method']);
        $this->assertContains('patch', $out['callables']);
        $this->assertNotContains('post', $out['callables']);
        $this->assertFalse($out['dispatch_allowed']);
    }
}
