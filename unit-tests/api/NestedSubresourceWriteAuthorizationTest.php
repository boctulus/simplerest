<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class NestedSubresourceWriteAuthorizationTest extends TestCase
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

        $this->assertIsResource($process, 'Could not start the isolated nested-write probe');
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        $this->assertSame(0, $exitCode, "Probe failed. stdout: $stdout stderr: $stderr");

        return json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
    }

    public function test_parent_create_permission_does_not_authorize_nested_user_roles(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'body' => [
                'username' => 'nested-user',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame('1:n', $out['relation_types']['user_roles']);
        $this->assertSame(403, $out['response']['status']);
        $this->assertCount(0, $out['state']['users']);
        $this->assertCount(0, $out['state']['user_roles']);
    }

    public function test_parent_create_permission_does_not_authorize_nested_pivot_link(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'seed_tag' => true,
            'body' => [
                'username' => 'nested-user',
                'tags' => [['id' => 1]],
            ],
        ]);

        $this->assertSame('n:m', $out['relation_types']['tags']);
        $this->assertSame(403, $out['response']['status']);
        $this->assertCount(0, $out['state']['users']);
        $this->assertCount(0, $out['state']['users_tags']);
    }

    public function test_parent_update_permission_does_not_authorize_nested_put(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'body' => [
                'username' => 'updated-user',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame('1:n', $out['relation_types']['user_roles']);
        $this->assertSame(403, $out['response']['status']);
        $this->assertSame('existing-user', $out['state']['users'][0]['username']);
        $this->assertCount(1, $out['state']['user_roles']);
        $this->assertSame(2, (int) $out['state']['user_roles'][0]['role_id']);
    }

    public function test_parent_update_permission_does_not_authorize_nested_patch(): void
    {
        $out = $this->probe([
            'method' => 'PATCH',
            'seed_user' => true,
            'body' => [
                'username' => 'updated-user',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame('1:n', $out['relation_types']['user_roles']);
        $this->assertSame(403, $out['response']['status']);
        $this->assertSame('existing-user', $out['state']['users'][0]['username']);
        $this->assertCount(1, $out['state']['user_roles']);
        $this->assertSame(2, (int) $out['state']['user_roles'][0]['role_id']);
    }

    public function test_explicit_policy_can_authorize_allowlisted_put_child_creation(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'nested_policy' => ['PUT' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'updated-user',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame(200, $out['response']['status']);
        $this->assertSame('updated-user', $out['state']['users'][0]['username']);
        $this->assertCount(1, $out['state']['user_roles']);
        $this->assertSame(9, (int) $out['state']['user_roles'][0]['role_id']);
        $this->assertSame([
            ['operation' => 'PUT', 'relation' => 'user_roles', 'parent_id' => 1, 'parent_username' => 'updated-user'],
        ], $out['policy_calls']);
    }

    public function test_explicit_policy_can_authorize_owned_child_update(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'nested_policy' => ['PUT' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'updated-user',
                'user_roles' => [['id' => 1, 'role_id' => 9]],
            ],
        ]);

        $this->assertSame(200, $out['response']['status']);
        $this->assertSame('updated-user', $out['state']['users'][0]['username']);
        $this->assertCount(1, $out['state']['user_roles']);
        $this->assertSame(9, (int) $out['state']['user_roles'][0]['role_id']);
    }

    public function test_explicit_policy_can_authorize_allowlisted_patch_child_creation(): void
    {
        $out = $this->probe([
            'method' => 'PATCH',
            'seed_user' => true,
            'nested_policy' => ['PATCH' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'updated-user',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame(200, $out['response']['status']);
        $this->assertSame('updated-user', $out['state']['users'][0]['username']);
        $this->assertCount(1, $out['state']['user_roles']);
        $this->assertSame(9, (int) $out['state']['user_roles'][0]['role_id']);
        $this->assertSame('PATCH', $out['policy_calls'][0]['operation']);
    }

    public function test_nested_child_id_from_another_parent_is_rejected(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'seed_other_user' => true,
            'nested_policy' => ['PUT' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'updated-user',
                'user_roles' => [['id' => 2, 'role_id' => 9]],
            ],
        ]);

        $this->assertSame(403, $out['response']['status']);
        $this->assertSame('existing-user', $out['state']['users'][0]['username']);
        $this->assertSame('other-user', $out['state']['users'][1]['username']);
        $this->assertCount(2, $out['state']['user_roles']);
        $this->assertSame(2, (int) $out['state']['user_roles'][0]['role_id']);
        $this->assertSame(3, (int) $out['state']['user_roles'][1]['role_id']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_parent_create_without_nested_payload_remains_allowed(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'body' => ['username' => 'ordinary-user'],
        ]);

        $this->assertSame(201, $out['response']['status']);
        $this->assertCount(1, $out['state']['users']);
        $this->assertCount(0, $out['state']['user_roles']);
    }

    public function test_invalid_nested_child_rolls_back_put_changes(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'nested_policy' => ['PUT' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'updated-user',
                'user_roles' => [['role_id' => null]],
            ],
        ]);

        $this->assertSame(400, $out['response']['status']);
        $this->assertSame('existing-user', $out['state']['users'][0]['username']);
        $this->assertCount(1, $out['state']['user_roles']);
        $this->assertSame(2, (int) $out['state']['user_roles'][0]['role_id']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_invalid_parent_put_does_not_persist_nested_child(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'nested_policy' => ['PUT' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => '',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame(400, $out['response']['status']);
        $this->assertSame('existing-user', $out['state']['users'][0]['username']);
        $this->assertCount(1, $out['state']['user_roles']);
        $this->assertSame(2, (int) $out['state']['user_roles'][0]['role_id']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_parent_after_check_hook_runs_before_nested_put_writes(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'fail_parent_hook' => true,
            'nested_policy' => ['PUT' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'updated-user',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame(500, $out['response']['status']);
        $this->assertSame('Resource update failed', $out['response']['error']['message']);
        $this->assertSame('existing-user', $out['state']['users'][0]['username']);
        $this->assertCount(1, $out['state']['user_roles']);
        $this->assertSame(2, (int) $out['state']['user_roles'][0]['role_id']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_explicit_policy_can_authorize_allowlisted_child_creation(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'nested_policy' => ['POST' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'authorized-user',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame(201, $out['response']['status']);
        $this->assertSame(9, (int) $out['state']['user_roles'][0]['role_id']);
        $this->assertSame([
            ['operation' => 'POST', 'relation' => 'user_roles', 'parent_id' => null, 'parent_username' => 'authorized-user'],
        ], $out['policy_calls']);
    }

    public function test_nested_audit_metadata_is_rejected_when_not_allowlisted(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'nested_policy' => ['POST' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'metadata-user',
                'user_roles' => [['role_id' => 9, 'created_by' => 999]],
            ],
        ]);

        $this->assertSame(403, $out['response']['status']);
        $this->assertCount(0, $out['state']['users']);
        $this->assertCount(0, $out['state']['user_roles']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_parent_foreign_key_cannot_be_allowlisted_for_nested_input(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'nested_policy' => ['POST' => ['user_roles' => ['role_id', 'user_id']]],
            'body' => [
                'username' => 'foreign-key-user',
                'user_roles' => [['role_id' => 9, 'user_id' => 77]],
            ],
        ]);

        $this->assertSame(403, $out['response']['status']);
        $this->assertCount(0, $out['state']['users']);
        $this->assertCount(0, $out['state']['user_roles']);
    }

    public function test_application_policy_can_deny_nested_privileged_role_before_write(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'denied_role_ids' => [9],
            'nested_policy' => ['POST' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'restricted-role-user',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame(403, $out['response']['status']);
        $this->assertCount(0, $out['state']['users']);
        $this->assertCount(0, $out['state']['user_roles']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_invalid_nested_child_rolls_back_post_parent(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'nested_policy' => ['POST' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'invalid-child-user',
                'user_roles' => [['role_id' => null]],
            ],
        ]);

        $this->assertSame(400, $out['response']['status']);
        $this->assertCount(0, $out['state']['users']);
        $this->assertCount(0, $out['state']['user_roles']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_mixed_valid_and_invalid_post_children_roll_back_parent(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'nested_policy' => ['POST' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'mixed-child-user',
                'user_roles' => [['role_id' => 9], ['role_id' => null]],
            ],
        ]);

        $this->assertSame(400, $out['response']['status']);
        $this->assertCount(0, $out['state']['users']);
        $this->assertCount(0, $out['state']['user_roles']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_pivot_sql_error_rolls_back_parent_and_prior_child_insert(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'seed_tag' => true,
            'fail_pivot_insert' => true,
            'nested_policy' => [
                'POST' => [
                    'user_roles' => ['role_id'],
                    'tags' => [],
                ],
            ],
            'body' => [
                'username' => 'atomic-user',
                'user_roles' => [['role_id' => 9]],
                'tags' => [['id' => 1]],
            ],
        ]);

        $this->assertSame(500, $out['response']['status']);
        $this->assertSame('Nested resource write failed', $out['response']['error']['message']);
        $this->assertStringNotContainsString('injected pivot SQL detail', json_encode($out['response']['error']));
        $this->assertCount(0, $out['state']['users']);
        $this->assertCount(0, $out['state']['user_roles']);
        $this->assertCount(0, $out['state']['users_tags']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_post_parent_hook_changes_are_revalidated_before_writes(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'parent_username_after_post_hook' => '',
            'nested_policy' => ['POST' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'valid-before-hook',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame(400, $out['response']['status']);
        $this->assertCount(0, $out['state']['users']);
        $this->assertCount(0, $out['state']['user_roles']);
        $this->assertSame([], $out['policy_calls']);
    }

    public function test_nested_sql_error_is_redacted_and_rolls_back_post(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'fail_role_insert' => true,
            'nested_policy' => ['POST' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'sql-error-user',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame(500, $out['response']['status']);
        $this->assertSame('Nested resource write failed', $out['response']['error']['message']);
        $this->assertStringNotContainsString('injected SQL detail', json_encode($out['response']['error']));
        $this->assertCount(0, $out['state']['users']);
        $this->assertCount(0, $out['state']['user_roles']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_nested_sql_error_is_redacted_and_rolls_back_put(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'fail_role_insert' => true,
            'nested_policy' => ['PUT' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'updated-user',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame(500, $out['response']['status']);
        $this->assertSame('Resource update failed', $out['response']['error']['message']);
        $this->assertStringNotContainsString('injected SQL detail', json_encode($out['response']['error']));
        $this->assertSame('existing-user', $out['state']['users'][0]['username']);
        $this->assertCount(1, $out['state']['user_roles']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_explicit_policy_can_authorize_existing_pivot_link(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'seed_tag' => true,
            'nested_policy' => ['POST' => ['tags' => []]],
            'body' => [
                'username' => 'tagged-user',
                'tags' => [['id' => 1]],
            ],
        ]);

        $this->assertSame(201, $out['response']['status']);
        $this->assertCount(1, $out['state']['users_tags']);
        $this->assertSame(1, (int) $out['state']['users_tags'][0]['tag_id']);
        $this->assertSame('tags', $out['policy_calls'][0]['relation']);
    }

    public function test_explicit_policy_can_authorize_scalar_post_pivot_link(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'seed_tag' => true,
            'parent_username_after_post_hook' => 'hook-adjusted-user',
            'nested_policy' => ['POST' => ['tags' => []]],
            'body' => [
                'username' => 'tagged-user',
                'tags' => [1],
            ],
        ]);

        $this->assertSame(201, $out['response']['status']);
        $this->assertSame('hook-adjusted-user', $out['state']['users'][0]['username']);
        $this->assertCount(1, $out['state']['users_tags']);
        $this->assertSame(1, (int) $out['state']['users_tags'][0]['tag_id']);
        $this->assertSame('hook-adjusted-user', $out['policy_calls'][0]['parent_username']);
    }

    public function test_post_pivot_selector_cannot_mix_target_id_and_related_fields(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'seed_tag' => true,
            'nested_policy' => ['POST' => ['tags' => ['name']]],
            'body' => [
                'username' => 'ambiguous-tag-user',
                'tags' => [['id' => 1, 'name' => 'changed-tag']],
            ],
        ]);

        $this->assertSame(400, $out['response']['status']);
        $this->assertCount(0, $out['state']['users']);
        $this->assertCount(0, $out['state']['users_tags']);
        $this->assertSame('existing-tag', $out['state']['tags'][0]['name']);
    }

    public function test_explicit_policy_can_authorize_put_pivot_link(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'seed_tag' => true,
            'nested_policy' => ['PUT' => ['tags' => []]],
            'body' => [
                'username' => 'updated-user',
                'tags' => [['tag_id' => 1]],
            ],
        ]);

        $this->assertSame(200, $out['response']['status']);
        $this->assertSame('updated-user', $out['state']['users'][0]['username']);
        $this->assertCount(1, $out['state']['users_tags']);
        $this->assertSame(1, (int) $out['state']['users_tags'][0]['tag_id']);
        $this->assertSame(1, $out['policy_calls'][0]['parent_id']);
    }

    public function test_put_nested_policy_receives_parent_payload_after_parent_hook(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'parent_username_after_hook' => 'hook-adjusted-user',
            'nested_policy' => ['PUT' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'request-user',
                'user_roles' => [['role_id' => 9]],
            ],
        ]);

        $this->assertSame(200, $out['response']['status']);
        $this->assertSame('hook-adjusted-user', $out['state']['users'][0]['username']);
        $this->assertSame('hook-adjusted-user', $out['policy_calls'][0]['parent_username']);
    }

    public function test_explicit_policy_can_update_related_fields_and_link_pivot(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'seed_tag' => true,
            'nested_policy' => ['PUT' => ['tags' => ['name']]],
            'body' => [
                'username' => 'updated-user',
                'tags' => [['tag_id' => 1, 'name' => 'updated-tag']],
            ],
        ]);

        $this->assertSame(200, $out['response']['status']);
        $this->assertSame('updated-tag', $out['state']['tags'][0]['name']);
        $this->assertCount(1, $out['state']['users_tags']);
        $this->assertSame(1, (int) $out['state']['users_tags'][0]['tag_id']);
    }

    public function test_put_pivot_can_use_related_primary_key_as_selector(): void
    {
        $out = $this->probe([
            'method' => 'PUT',
            'seed_user' => true,
            'seed_tag' => true,
            'nested_policy' => ['PUT' => ['tags' => ['name']]],
            'body' => [
                'username' => 'updated-user',
                'tags' => [['id' => 1, 'name' => 'id-selected-tag']],
            ],
        ]);

        $this->assertSame(200, $out['response']['status']);
        $this->assertSame('id-selected-tag', $out['state']['tags'][0]['name']);
        $this->assertCount(1, $out['state']['users_tags']);
        $this->assertSame(1, (int) $out['state']['users_tags'][0]['tag_id']);
    }

    public function test_unconnected_nested_array_is_rejected_before_parent_write(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'body' => [
                'username' => 'unsupported-user',
                'unconnected' => [['sensitive' => true]],
            ],
        ]);

        $this->assertSame(400, $out['response']['status']);
        $this->assertCount(0, $out['state']['users']);
        $this->assertFalse($out['transaction_open']);
    }

    public function test_malformed_nested_collection_is_rejected_before_parent_write(): void
    {
        $out = $this->probe([
            'method' => 'POST',
            'nested_policy' => ['POST' => ['user_roles' => ['role_id']]],
            'body' => [
                'username' => 'malformed-user',
                'user_roles' => ['role_id' => 9],
            ],
        ]);

        $this->assertSame(400, $out['response']['status']);
        $this->assertCount(0, $out['state']['users']);
        $this->assertFalse($out['transaction_open']);
    }
}
