<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class ApiAuthorizationDispatchTest extends TestCase
{
    private function probe(
        string $originalMethod,
        ?string $overrideMethod,
        array $rolePermissions,
        ?int $tablePermissions,
        string $overrideSource = 'header',
        array $specialPermissions = []
    ): array
    {
        $fixture = __DIR__ . '/fixtures/api_authorization_dispatch_probe.php';
        $payload = base64_encode(json_encode([
            'original_method' => $originalMethod,
            'override_method' => $overrideMethod,
            'override_source' => $overrideSource,
            'role_permissions' => $rolePermissions,
            'table_permissions' => $tablePermissions,
            'special_permissions' => $specialPermissions,
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

    public function test_user_post_bitmask_adds_post_callable(): void
    {
        $out = $this->probe('POST', null, [], 4);

        $this->assertSame('post', $out['dispatch_method']);
        $this->assertNotContains('get', $out['callables']);
        $this->assertContains('post', $out['callables']);
        $this->assertTrue($out['dispatch_allowed']);
    }

    public function test_user_patch_bitmask_adds_dispatched_patch_callable(): void
    {
        $out = $this->probe('PATCH', null, [], 2);

        $this->assertSame('patch', $out['dispatch_method']);
        $this->assertContains('patch', $out['callables']);
        $this->assertNotContains('putch', $out['callables']);
        $this->assertTrue($out['dispatch_allowed']);
    }

    public function test_user_zero_bitmask_replaces_role_callable(): void
    {
        $out = $this->probe('POST', null, ['create' => true], 0);

        $this->assertNotContains('post', $out['callables']);
        $this->assertFalse(in_array('get', $out['callables'], true));
        $this->assertFalse($out['dispatch_allowed']);
    }

    public function test_user_update_and_delete_bits_match_dispatched_callables(): void
    {
        foreach ([['PUT', 2, 'put'], ['DELETE', 1, 'delete']] as [$method, $mask, $callable]) {
            $out = $this->probe($method, null, [], $mask);

            $this->assertSame(strtolower($method), $out['dispatch_method']);
            $this->assertContains($callable, $out['callables']);
            $this->assertTrue($out['dispatch_allowed']);
        }
    }

    public function test_user_read_mask_replaces_role_grants_and_controls_owner_scope_bypass(): void
    {
        $rolePermissions = ['list' => true, 'show' => true, 'list_all' => true, 'show_all' => true];
        $list = $this->probe('GET', null, $rolePermissions, 16);
        $show = $this->probe('GET', null, $rolePermissions, 8);

        $this->assertContains('get', $list['callables']);
        $this->assertSame([
            'is_listable' => true,
            'is_retrievable' => false,
            'may_list_all' => false,
            'may_show_all' => false,
        ], $list['read_state']);
        $this->assertContains('get', $show['callables']);
        $this->assertSame([
            'is_listable' => false,
            'is_retrievable' => true,
            'may_list_all' => false,
            'may_show_all' => false,
        ], $show['read_state']);
    }

    public function test_global_acl_capabilities_remain_effective_with_user_mask(): void
    {
        $read = $this->probe('GET', null, [], 0, 'header', ['read_all']);
        $this->assertContains('get', $read['callables']);
        $this->assertSame([
            'is_listable' => true,
            'is_retrievable' => true,
            'may_list_all' => true,
            'may_show_all' => true,
        ], $read['read_state']);

        foreach ([['POST', 'post'], ['PUT', 'put'], ['PATCH', 'patch'], ['DELETE', 'delete']] as [$method, $callable]) {
            $write = $this->probe($method, null, [], 0, 'header', ['write_all']);

            $this->assertContains($callable, $write['callables']);
            $this->assertTrue($write['dispatch_allowed']);
        }
    }

    public function test_header_override_uses_effective_method_for_acl_and_dispatch(): void
    {
        $out = $this->probe('POST', 'PATCH', ['update' => true], null);

        $this->assertSame('POST', $out['original_method']);
        $this->assertSame('patch', $out['dispatch_method']);
        $this->assertContains('patch', $out['callables']);
        $this->assertNotContains('post', $out['callables']);
        $this->assertTrue($out['dispatch_allowed']);
    }

    public function test_header_override_uses_effective_method_for_table_update_permission(): void
    {
        $out = $this->probe('POST', 'PATCH', [], 2);

        $this->assertSame('POST', $out['original_method']);
        $this->assertSame('patch', $out['dispatch_method']);
        $this->assertContains('patch', $out['callables']);
        $this->assertNotContains('post', $out['callables']);
        $this->assertTrue($out['dispatch_allowed']);
    }

    public function test_url_override_from_patch_to_post_uses_same_method_for_acl_and_dispatch(): void
    {
        $out = $this->probe('PATCH', 'POST', ['create' => true], null, 'url');

        $this->assertSame('PATCH', $out['original_method']);
        $this->assertSame('post', $out['dispatch_method']);
        $this->assertContains('post', $out['callables']);
        $this->assertNotContains('patch', $out['callables']);
        $this->assertTrue($out['dispatch_allowed']);
    }
}
