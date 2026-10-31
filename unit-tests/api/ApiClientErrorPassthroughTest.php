<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

/*
    ApiController lets client errors (int code 400-499) escape towards the configured
    front error handler instead of flattening them into a generic 500.

    Regression: a model invariant refusing a PUT/PATCH (e.g. restoring a trashed row
    whose parent is gone, 409) was answered as "Resource update failed" 500, and in
    consumer copies of the controller even dumped the SQL log first. The core
    ErrorHandler already maps getCode() to the HTTP status; the controller just never
    let the exception reach it.

    Uses the isolated nested-write probe (SQLite in memory, faked request/response).
*/
class ApiClientErrorPassthroughTest extends TestCase
{
    private function probe(array $input): array
    {
        $fixture = __DIR__ . '/fixtures/nested_subresource_write_probe.php';
        $payload = base64_encode(json_encode($input, JSON_THROW_ON_ERROR));
        $pipes = [];
        $process = proc_open(
            [PHP_BINARY, $fixture, $payload],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );

        $this->assertIsResource($process, 'Could not start the isolated probe');
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        $this->assertSame(0, $exitCode, "Probe failed. stdout: $stdout stderr: $stderr");

        return json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
    }

    public function test_model_refusal_on_put_escapes_with_its_status(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'model_update_failure_code' => 409,
            'body' => ['username' => 'updated-user'],
        ]);

        $this->assertSame(['class' => 'DomainException', 'code' => 409, 'message' => 'probe model refusal'], $out['escaped']);
        $this->assertSame([], $out['response'], 'the controller must not answer a generic error itself');
        $this->assertSame('existing-user', $out['state']['users'][0]['username']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_model_refusal_on_patch_escapes_with_its_status(): void
    {
        $out = $this->probe([
            'method' => 'PATCH',
            'seed_user' => true,
            'model_update_failure_code' => 403,
            'body' => ['username' => 'updated-user'],
        ]);

        $this->assertSame(403, $out['escaped']['code'] ?? null);
        $this->assertSame('existing-user', $out['state']['users'][0]['username']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_client_error_from_a_put_hook_rolls_back_nested_writes_and_escapes(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'fail_parent_hook' => true,
            'parent_hook_failure_code' => 422,
            'nested_policy' => ['PUT' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'updated-user',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame(422, $out['escaped']['code'] ?? null);
        $this->assertSame('existing-user', $out['state']['users'][0]['username']);
        $this->assertCount(1, $out['state']['user_roles']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_client_error_from_a_post_hook_escapes_without_creating(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'fail_parent_hook' => true,
            'parent_hook_failure_code' => 409,
            'body' => ['username' => 'new-user'],
        ]);

        $this->assertSame(409, $out['escaped']['code'] ?? null);
        $this->assertSame([], $out['state']['users']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_server_errors_keep_the_generic_500(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'model_update_failure_code' => 500,
            'body' => ['username' => 'updated-user'],
        ]);

        $this->assertNull($out['escaped']);
        $this->assertSame(500, $out['response']['status']);
        $this->assertSame('Resource update failed', $out['response']['error']['message']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_collections_write_catches_pass_through_client_errors(): void
    {
        $source = file_get_contents(__DIR__ . '/../../src/framework/Api/Collections.php');
        $guarded = preg_match_all('/catch \(\\\\Exception \$e\) \{\s*\$this->rethrowClientError\(\$e\);/', $source);

        $this->assertSame(3, $guarded, 'post, update and delete of Collections must re-throw client errors');
    }
}
