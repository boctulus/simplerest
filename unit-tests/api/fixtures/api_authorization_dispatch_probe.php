<?php

namespace Boctulus\Simplerest\Core\Api {
    function acl() { return \ApiAuthorizationProbeHarness::$acl; }
    function auth() { return \ApiAuthorizationProbeHarness::$auth; }
    function request() { return \ApiAuthorizationProbeHarness::$request; }
    function response() { return new \stdClass(); }
    function cors() { }
    function is_cli(): bool { return false; }
}

namespace Boctulus\Simplerest\Core\Controllers {
    function is_cli(): bool { return false; }
}

namespace {
    function env(string $key, $default = null) { return $default; }
    function namespace_url(): string { return 'Boctulus\\Simplerest'; }
}

namespace Boctulus\Simplerest\Core\Libs\Impersonation {
    // These test doubles isolate ApiController's ACL construction from the
    // optional impersonation integration while preserving its constructor path.
    class ImpersonationRequestContext {
        public static function hydrateFromToken(array $auth): object { return (object) []; }
    }

    class ImpersonationManager {
        public static function getInstance(): self { return new self(); }
        public function enforceReadOnly($context, string $method, string $path, string $table): void { }
    }
}

namespace {
    $root = dirname(__DIR__, 3);
    require_once $root . '/vendor/autoload.php';
    require_once $root . '/config/constants.php';
    error_reporting(E_ALL);
    ini_set('display_errors', 'stderr');
    ini_set('log_errors', '0');
    register_shutdown_function(static function (): void {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            fwrite(STDERR, $error['message'] . ' at ' . $error['file'] . ':' . $error['line'] . PHP_EOL);
        }
    });

    class ApiAuthorizationProbeHarness {
        public static $acl;
        public static $auth;
        public static $request;
    }

    class ApiAuthorizationProbeAcl {
        public function __construct(
            private array $rolePermissions,
            private ?int $tablePermissions
        ) { }

        public function hasSpecialPermission(string $permission): bool { return false; }
        public function hasResourcePermission(string $permission, string $resource): bool {
            return !empty($this->rolePermissions[$permission]);
        }
        public function getTbPermissions(string $resource, bool $unpacked = true): ?int {
            return $this->tablePermissions;
        }
    }

    class ApiAuthorizationProbeAuth {
        public function check(): array { return ['impersonated_by' => null]; }
        public function getPermissions(): array { return []; }
    }

    class ApiAuthorizationProbeRequest {
        public function getTenantId(): ?int { return null; }
    }

    class ApiAuthorizationProbeController extends \Boctulus\Simplerest\Core\Api\ApiController {
        public function __construct() {
            $this->model_name = 'WidgetsModel';
            parent::__construct();
        }

        protected function getModelInstance($fetch_mode = 'ASSOC', bool $reuse = false) {
            return new \stdClass();
        }
    }

    $input = json_decode(base64_decode($argv[1] ?? ''), true, 512, JSON_THROW_ON_ERROR);
    ApiAuthorizationProbeHarness::$acl = new ApiAuthorizationProbeAcl(
        $input['role_permissions'] ?? [],
        $input['table_permissions'] ?? null
    );
    ApiAuthorizationProbeHarness::$auth = new ApiAuthorizationProbeAuth();
    ApiAuthorizationProbeHarness::$request = new ApiAuthorizationProbeRequest();

    \Boctulus\Simplerest\Core\Libs\Config::set('error_handling', false);
    \Boctulus\Simplerest\Core\Libs\Config::set('remove_api_slug', false);
    \Boctulus\Simplerest\Core\Libs\Config::set('method_override', [
        'by_url' => ($input['override_source'] ?? 'header') === 'url',
        'by_header' => ($input['override_source'] ?? 'header') === 'header',
    ]);

    $_SERVER['REQUEST_METHOD'] = strtoupper($input['original_method']);
    $_SERVER['REQUEST_URI'] = '/api/v1/widgets';
    \Boctulus\Simplerest\Core\Request::setInstance(null);
    \Boctulus\Simplerest\Core\Request::getInstance();

    $headers = new \ReflectionProperty(\Boctulus\Simplerest\Core\Request::class, 'headers');
    $headers->setAccessible(true);
    $headers->setValue(null, ($input['override_source'] ?? 'header') === 'header' && $input['override_method'] !== null
        ? ['x-http-method-override' => strtoupper($input['override_method'])]
        : []
    );

    $query = new \ReflectionProperty(\Boctulus\Simplerest\Core\Request::class, 'query_arr');
    $query->setAccessible(true);
    $query->setValue(null, ($input['override_source'] ?? 'header') === 'url' && $input['override_method'] !== null
        ? ['_method' => strtoupper($input['override_method'])]
        : []
    );

    $controller = new ApiAuthorizationProbeController();

    [, $dispatchMethod] = (new \Boctulus\Simplerest\Core\Handlers\ApiHandler())
        ->resolve(['api', 'v1', 'widgets']);

    echo json_encode([
        'original_method' => $_SERVER['REQUEST_METHOD'],
        'override_method' => $input['override_method'],
        'dispatch_method' => $dispatchMethod,
        'callables' => $controller->getCallable(),
        'dispatch_allowed' => in_array($dispatchMethod, $controller->getCallable(), true),
    ], JSON_THROW_ON_ERROR);
}
