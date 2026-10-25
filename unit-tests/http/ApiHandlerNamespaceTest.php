<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Handlers\ApiHandler;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Request;

require_once __DIR__ . '/../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
    return;
}

require_once __DIR__ . '/../../app.php';

/**
 * Controller namespace resolved by ApiHandler for /api/{version}/{resource}.
 *
 * Regression: ApiHandler built "\Controllers\api\" (lowercase) while application controllers
 * live in app/Controllers/Api/ with namespace "\Controllers\Api". Windows hides the mismatch
 * because its filesystem is case-insensitive; on Linux PSR-4 looks for app/Controllers/api/
 * and every automatic endpoint answered 404 CLASS_NOT_FOUND.
 */
class ApiHandlerNamespaceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Request::setInstance(null);
    }

    protected function tearDown(): void
    {
        Request::setInstance(null);
        parent::tearDown();
    }

    private function resolveClass(string $resource): string
    {
        $params = Config::get()['remove_api_slug'] ? ['v1', $resource] : ['api', 'v1', $resource];

        [$class_name] = (new ApiHandler())->resolve($params);

        return $class_name;
    }

    public function testApplicationControllersResolveToApiNamespace(): void
    {
        $this->assertSame(namespace_url() . '\\Controllers\\Api\\Users', $this->resolveClass('users'));
        $this->assertSame(namespace_url() . '\\Controllers\\Api\\UserRoles', $this->resolveClass('user_roles'));
    }

    public function testResolvedNamespaceMatchesControllerDirectoryCaseSensitively(): void
    {
        $segments = explode('\\', $this->resolveClass('users'));
        $directory = $segments[count($segments) - 2];

        // scandir() reports the real on-disk casing even on case-insensitive filesystems.
        $this->assertContains($directory, scandir(ROOT_PATH . 'app' . DIRECTORY_SEPARATOR . 'Controllers'));
    }

    /** TrashCan resolved entity controllers with the same lowercase namespace. */
    public function testFrameworkNeverReferencesLowercaseApiControllerNamespace(): void
    {
        $offenders = [];
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOT_PATH . 'src' . DIRECTORY_SEPARATOR . 'framework'));

        foreach ($files as $file) {
            if ($file->getExtension() === 'php' && preg_match('/Controllers\\\\+api\\\\+[\'"A-Z]/', file_get_contents($file->getPathname()))) {
                $offenders[] = $file->getPathname();
            }
        }

        $this->assertSame([], $offenders);
    }

    public function testFrameworkControllersKeepCoreNamespace(): void
    {
        $this->assertSame(namespace_url() . '\\Core\\Api\\TrashCan', $this->resolveClass('trash_can'));
        $this->assertSame(namespace_url() . '\\Core\\Api\\Collections', $this->resolveClass('collections'));
    }
}
