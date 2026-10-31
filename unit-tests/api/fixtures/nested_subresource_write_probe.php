<?php

namespace Boctulus\Simplerest\Core\Api {
    function acl() { return \NestedSubresourceProbeHarness::$acl; }
    function auth() { return \NestedSubresourceProbeHarness::$auth; }
    function request() { return \NestedSubresourceProbeHarness::$request; }
    function response(...$args) { return \NestedSubresourceProbeHarness::$response; }
    function error(...$args) { return response()->error(...$args); }
    function get_pivot(array $tables, ?string $tenant_id = null) { return \Boctulus\Simplerest\Core\Libs\DBRels::getPivot($tables, $tenant_id); }
    function is_cli(): bool { return true; }
}

namespace Boctulus\Simplerest\Core\Controllers {
    function is_cli(): bool { return true; }
}

namespace Boctulus\Simplerest\Core\Libs\Impersonation {
    class ImpersonationRequestContext
    {
        public static function hydrateFromToken(array $auth): object { return (object) []; }
    }

    class ImpersonationManager
    {
        public static function getInstance(): self { return new self(); }
        public function enforceReadOnly($context, string $method, string $path, string $table): void { }
    }
}

namespace {
    $root = dirname(__DIR__, 3);
    require_once $root . '/vendor/autoload.php';

    if (!defined('SCHEMA_PATH')) {
        define('SCHEMA_PATH', __DIR__ . '/nested_schemas/');
    }

    require_once $root . '/config/constants.php';
    require_once $root . '/src/framework/Helpers/db.php';

    class NestedSubresourceProbeHalt extends Error { }

    class NestedSubresourceProbeHarness
    {
        public static $acl;
        public static $auth;
        public static $request;
        public static $response;
        public static array $nestedPolicy = [];
        public static array $policyCalls = [];
        public static bool $failParentHook = false;
        public static int $parentHookFailureCode = 0;
        public static int $modelUpdateFailureCode = 0;
        public static array $deniedRoleIds = [];
        public static ?string $parentUsernameAfterHook = null;
        public static ?string $parentUsernameAfterPostHook = null;
    }

    class NestedSubresourceProbeAcl
    {
        public function getTbPermissions(string $resource, bool $unpacked = true): ?int { return null; }
        public function hasSpecialPermission(string $permission): bool { return false; }
        public function hasResourcePermission(string $permission, string $resource): bool
        {
            return $resource === 'users' && in_array($permission, ['create', 'update'], true);
        }
    }

    class NestedSubresourceProbeAuth
    {
        public function uid(): int { return 7; }
    }

    class NestedSubresourceProbeRequest
    {
        public function __construct(private array $body) { }
        public function getTenantId(): ?string { return null; }
        public function getBody(bool $assoc = true): array { return $this->body; }
        public function shiftQuery(string $key, $default = null) { return $default; }
    }

    class NestedSubresourceProbeResponse
    {
        public array $result = [];

        public function send($data = null, ?int $status = 200): void
        {
            $this->result = ['status' => $status, 'data' => $data];
            throw new NestedSubresourceProbeHalt();
        }

        public function error($error = null, ?int $status = null, $detail = null, ?string $location = null): void
        {
            $this->result = [
                'status' => $status ?? 500,
                'error' => ['message' => $error, 'detail' => $detail, 'location' => $location],
            ];
            throw new NestedSubresourceProbeHalt();
        }

        public function code(int $status): self
        {
            $this->result['status'] = $status;
            return $this;
        }
    }

    class NestedSubresourceProbeWebhookPublisher extends \Boctulus\Simplerest\Core\Libs\WebhookPublisher
    {
        public function __construct() { }
        public function publish(\Boctulus\Simplerest\Core\Libs\WebhookEvent $event): void { }
    }

    class NestedSubresourceProbeController extends \Boctulus\Simplerest\Core\Api\ApiController
    {
        protected static $connect_to = ['user_roles', 'tags'];

        public function __construct()
        {
            $this->model_name = 'UsersModel';
            $this->table_name = 'users';
            parent::__construct();
        }

        protected function getModelInstance($fetch_mode = 'ASSOC', bool $reuse = false)
        {
            return new \Boctulus\Simplerest\Models\nested_test\UsersModel(true);
        }

        protected function getWebhookPublisher(): \Boctulus\Simplerest\Core\Libs\WebhookPublisher
        {
            return new NestedSubresourceProbeWebhookPublisher();
        }

        protected function authorizeNestedWrite(string $operation, string $relation, array $payload, array $parentPayload, $parentId = null): ?array
        {
            NestedSubresourceProbeHarness::$policyCalls[] = [
                'operation' => $operation,
                'relation' => $relation,
                'parent_id' => $parentId,
                'parent_username' => $parentPayload['username'] ?? null,
            ];

            foreach ($payload as $row) {
                if (
                    $relation === 'user_roles'
                    && is_array($row)
                    && isset($row['role_id'])
                    && in_array($row['role_id'], NestedSubresourceProbeHarness::$deniedRoleIds, true)
                ) {
                    return null;
                }
            }

            return NestedSubresourceProbeHarness::$nestedPolicy[$operation][$relation] ?? null;
        }

        protected function onPostingAfterCheck($id, array &$data)
        {
            if (NestedSubresourceProbeHarness::$failParentHook) {
                throw new \RuntimeException('probe parent hook failure', NestedSubresourceProbeHarness::$parentHookFailureCode);
            }
            if (NestedSubresourceProbeHarness::$parentUsernameAfterPostHook !== null) {
                $data['username'] = NestedSubresourceProbeHarness::$parentUsernameAfterPostHook;
            }
        }

        protected function onPuttingAfterCheck($id, array &$data)
        {
            if (NestedSubresourceProbeHarness::$failParentHook) {
                throw new \RuntimeException('probe parent hook failure', NestedSubresourceProbeHarness::$parentHookFailureCode);
            }
            if (NestedSubresourceProbeHarness::$parentUsernameAfterHook !== null) {
                $data['username'] = NestedSubresourceProbeHarness::$parentUsernameAfterHook;
            }
        }
    }
}

namespace Boctulus\Simplerest\Schemas\nested_test {
    use Boctulus\Simplerest\Core\Interfaces\ISchema;

    class UsersSchema implements ISchema
    {
        public static function get(): array
        {
            return [
                'table_name' => 'users', 'id_name' => 'id', 'fields' => ['id', 'username', 'user_roles', 'tags'],
                'attr_types' => ['id' => 'INT', 'username' => 'STR', 'user_roles' => 'JSON', 'tags' => 'JSON'],
                'attr_type_detail' => [], 'primary' => ['id'], 'autoincrement' => 'id', 'nullable' => ['id', 'user_roles', 'tags'],
                'required' => ['username'], 'uniques' => [], 'fks' => [],
                'rules' => ['id' => ['type' => 'int'], 'username' => ['type' => 'string', 'required' => true], 'user_roles' => ['type' => 'array'], 'tags' => ['type' => 'array']],
                'relationships' => ['user_roles' => [['user_roles.user_id', 'users.id']]],
                'expanded_relationships' => ['user_roles' => [[['user_roles', 'user_id'], ['users', 'id']]]],
                'relationships_from' => [], 'expanded_relationships_from' => [],
            ];
        }
    }

    class UserRolesSchema implements ISchema
    {
        public static function get(): array
        {
            $relation = [[['users', 'id'], ['user_roles', 'user_id']]];
            return [
                'table_name' => 'user_roles', 'id_name' => 'id', 'fields' => ['id', 'user_id', 'role_id'],
                'attr_types' => ['id' => 'INT', 'user_id' => 'INT', 'role_id' => 'INT'], 'attr_type_detail' => [],
                'primary' => ['id'], 'autoincrement' => 'id', 'nullable' => ['id'], 'required' => ['user_id', 'role_id'],
                'uniques' => [], 'fks' => ['user_id'],
                'rules' => ['id' => ['type' => 'int'], 'user_id' => ['type' => 'int', 'required' => true], 'role_id' => ['type' => 'int', 'required' => true]],
                'relationships' => ['users' => [['users.id', 'user_roles.user_id']]],
                'expanded_relationships' => ['users' => $relation], 'relationships_from' => [], 'expanded_relationships_from' => ['users' => $relation],
            ];
        }
    }

    class TagsSchema implements ISchema
    {
        public static function get(): array
        {
            return [
                'table_name' => 'tags', 'id_name' => 'id', 'fields' => ['id', 'name'],
                'attr_types' => ['id' => 'INT', 'name' => 'STR'], 'attr_type_detail' => [], 'primary' => ['id'],
                'autoincrement' => 'id', 'nullable' => ['id'], 'required' => ['name'], 'uniques' => [], 'fks' => [],
                'rules' => ['id' => ['type' => 'int'], 'name' => ['type' => 'string', 'required' => true]],
                'relationships' => [], 'expanded_relationships' => [], 'relationships_from' => [], 'expanded_relationships_from' => [],
            ];
        }
    }

    class UsersTagsSchema implements ISchema
    {
        public static function get(): array
        {
            return [
                'table_name' => 'users_tags', 'id_name' => 'id', 'fields' => ['id', 'user_id', 'tag_id'],
                'attr_types' => ['id' => 'INT', 'user_id' => 'INT', 'tag_id' => 'INT'], 'attr_type_detail' => [],
                'primary' => ['id'], 'autoincrement' => 'id', 'nullable' => ['id'], 'required' => ['user_id', 'tag_id'],
                'uniques' => [], 'fks' => ['user_id', 'tag_id'],
                'rules' => ['id' => ['type' => 'int'], 'user_id' => ['type' => 'int', 'required' => true], 'tag_id' => ['type' => 'int', 'required' => true]],
                'relationships' => [], 'expanded_relationships' => [], 'relationships_from' => [], 'expanded_relationships_from' => [],
            ];
        }
    }
}

namespace Boctulus\Simplerest\Models\nested_test {
    class UsersModel extends \Boctulus\Simplerest\Core\Model
    {
        public function __construct(bool $connect = false) { parent::__construct($connect, \Boctulus\Simplerest\Schemas\nested_test\UsersSchema::class); }

        // A model invariant refusing the write, as an application model would.
        public function update(array $data, $set_updated_at = true)
        {
            if (\NestedSubresourceProbeHarness::$modelUpdateFailureCode !== 0) {
                throw new \DomainException('probe model refusal', \NestedSubresourceProbeHarness::$modelUpdateFailureCode);
            }
            return parent::update($data, $set_updated_at);
        }
    }

    class UserRolesModel extends \Boctulus\Simplerest\Core\Model
    {
        public function __construct(bool $connect = false) { parent::__construct($connect, \Boctulus\Simplerest\Schemas\nested_test\UserRolesSchema::class); }
    }

    class TagsModel extends \Boctulus\Simplerest\Core\Model
    {
        public function __construct(bool $connect = false) { parent::__construct($connect, \Boctulus\Simplerest\Schemas\nested_test\TagsSchema::class); }
    }

    class UsersTagsModel extends \Boctulus\Simplerest\Core\Model
    {
        public function __construct(bool $connect = false) { parent::__construct($connect, \Boctulus\Simplerest\Schemas\nested_test\UsersTagsSchema::class); }
    }
}

namespace {
    $input = json_decode(base64_decode($argv[1] ?? ''), true, 512, JSON_THROW_ON_ERROR);
    error_reporting(E_ALL);
    ini_set('display_errors', 'stderr');
    ini_set('log_errors', '0');

    $nestedTestConfig = new \ReflectionProperty(\Boctulus\Simplerest\Core\Libs\Config::class, 'data');
    $nestedTestConfig->setAccessible(true);
    $nestedTestConfig->setValue(null, [
        'db_connections' => [
            'nested_test' => ['driver' => 'sqlite', 'db_name' => ':memory:', 'host' => null, 'port' => null, 'user' => null, 'pass' => null, 'charset' => null, 'pdo_options' => null],
        ],
        'db_connection_default' => 'nested_test',
        'namespace' => 'Boctulus\\Simplerest',
        'error_handling' => false,
        'debug' => false,
        'pretty' => false,
    ]);

    \NestedSubresourceProbeHarness::$acl = new \NestedSubresourceProbeAcl();
    \NestedSubresourceProbeHarness::$auth = new \NestedSubresourceProbeAuth();
    \NestedSubresourceProbeHarness::$request = new \NestedSubresourceProbeRequest($input['body'] ?? []);
    \NestedSubresourceProbeHarness::$response = new \NestedSubresourceProbeResponse();
    \NestedSubresourceProbeHarness::$nestedPolicy = $input['nested_policy'] ?? [];
    \NestedSubresourceProbeHarness::$failParentHook = !empty($input['fail_parent_hook']);
    \NestedSubresourceProbeHarness::$parentHookFailureCode = (int) ($input['parent_hook_failure_code'] ?? 0);
    \NestedSubresourceProbeHarness::$modelUpdateFailureCode = (int) ($input['model_update_failure_code'] ?? 0);
    \NestedSubresourceProbeHarness::$deniedRoleIds = $input['denied_role_ids'] ?? [];
    \NestedSubresourceProbeHarness::$parentUsernameAfterHook = $input['parent_username_after_hook'] ?? null;
    \NestedSubresourceProbeHarness::$parentUsernameAfterPostHook = $input['parent_username_after_post_hook'] ?? null;

    $_SERVER['REQUEST_METHOD'] = strtoupper($input['method'] ?? 'POST');
    $_SERVER['REQUEST_URI'] = '/api/v1/users';
    \Boctulus\Simplerest\Core\Request::setInstance(null);
    \Boctulus\Simplerest\Core\Request::getInstance();

    $pdo = \Boctulus\Simplerest\Core\Libs\DB::getConnection();
    $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL)');
    $pdo->exec('CREATE TABLE user_roles (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, role_id INTEGER NOT NULL)');
    $pdo->exec('CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL)');
    $pdo->exec('CREATE TABLE users_tags (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, tag_id INTEGER NOT NULL)');

    if (!empty($input['seed_user'])) {
        $pdo->exec("INSERT INTO users (username) VALUES ('existing-user')");
        $pdo->exec('INSERT INTO user_roles (user_id, role_id) VALUES (1, 2)');
    }
    if (!empty($input['seed_other_user'])) {
        $pdo->exec("INSERT INTO users (username) VALUES ('other-user')");
        $pdo->exec('INSERT INTO user_roles (user_id, role_id) VALUES (2, 3)');
    }
    if (!empty($input['seed_tag'])) {
        $pdo->exec("INSERT INTO tags (name) VALUES ('existing-tag')");
    }
    if (!empty($input['fail_role_insert'])) {
        $pdo->exec("CREATE TRIGGER fail_user_roles_insert BEFORE INSERT ON user_roles BEGIN SELECT RAISE(ABORT, 'injected SQL detail'); END;");
    }
    if (!empty($input['fail_pivot_insert'])) {
        $pdo->exec("CREATE TRIGGER fail_users_tags_insert BEFORE INSERT ON users_tags BEGIN SELECT RAISE(ABORT, 'injected pivot SQL detail'); END;");
    }

    try {
        $controller = new \NestedSubresourceProbeController();
        $method = strtolower($input['method'] ?? 'POST');
        if ($method === 'put') {
            $controller->put((int) ($input['target_id'] ?? 1));
        } elseif ($method === 'patch') {
            $controller->patch((int) ($input['target_id'] ?? 1));
        } else {
            $controller->post();
        }
    } catch (\NestedSubresourceProbeHalt $halt) {
    } catch (\Throwable $error) {
        // A client error (4xx code) must escape the controller towards the front error handler.
        $code = $error->getCode();
        if (is_int($code) && $code >= 400 && $code < 500) {
            $escaped = ['class' => get_class($error), 'code' => $code, 'message' => $error->getMessage()];
        } else {
            fwrite(STDERR, get_class($error) . ': ' . $error->getMessage() . PHP_EOL . $error->getTraceAsString() . PHP_EOL);
            exit(2);
        }
    }

    $state = [
        'users' => $pdo->query('SELECT * FROM users')->fetchAll(PDO::FETCH_ASSOC),
        'user_roles' => $pdo->query('SELECT * FROM user_roles')->fetchAll(PDO::FETCH_ASSOC),
        'tags' => $pdo->query('SELECT * FROM tags')->fetchAll(PDO::FETCH_ASSOC),
        'users_tags' => $pdo->query('SELECT * FROM users_tags')->fetchAll(PDO::FETCH_ASSOC),
    ];

    echo json_encode([
        'response' => \NestedSubresourceProbeHarness::$response->result,
        'policy_calls' => \NestedSubresourceProbeHarness::$policyCalls,
        'relation_types' => [
            'user_roles' => get_rel_type('users', 'user_roles'),
            'tags' => get_rel_type('users', 'tags'),
        ],
        'state' => $state,
        'transaction_open' => $pdo->inTransaction(),
        'escaped' => $escaped ?? null,
    ], JSON_THROW_ON_ERROR);
}
