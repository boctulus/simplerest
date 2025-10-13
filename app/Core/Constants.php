<?php

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\Libs\Strings;

class Constants

{
    public const ROOT_PATH = __DIR__ . '/../../'; // -- OK 

    public const CONFIG_PATH = self::ROOT_PATH . 'config/';

    public const DOCS_PATH = self::ROOT_PATH . 'docs/';

    public const UPLOADS_PATH =self::ROOT_PATH . 'uploads/';

    public const STORAGE_PATH = self::ROOT_PATH . 'storage/';

    public const CACHE_PATH = self::ROOT_PATH . 'cache/';

    public const LOGS_PATH = self::ROOT_PATH . 'logs/';

    public const VENDOR_PATH = self::ROOT_PATH . 'vendor/';

    public const APP_PATH = self::ROOT_PATH . 'app/';

    public const BACKUP_PATH = self::ROOT_PATH . 'backup/';

    public const UPDATE_PATH = self::ROOT_PATH . 'updates/';

    public const CORE_PATH = self::APP_PATH . 'core/';

    public const CORE_INTERFACE_PATH = self::CORE_PATH . 'interfaces/';

    public const CORE_TRAIT_PATH = self::CORE_PATH . 'traits/';

    public const CORE_LIBS_PATH = self::CORE_PATH . 'libs/';

    public const CORE_HELPERS_PATH = self::CORE_PATH . 'helpers/';

    public const TEMPLATES_PATH = self::CORE_PATH . 'templates/';

    public const MODELS_PATH = self::APP_PATH . 'models/';

    public const SCHEMA_PATH = self::APP_PATH . 'schemas/';

    public const CRONOS_PATH = self::APP_PATH . 'Background/Cronjobs/';

    public const TASKS_PATH = self::APP_PATH . 'Background/Tasks/';

    public const COMMANDS_PATH = self::APP_PATH . 'Commands/';

    public const MIGRATIONS_PATH = self::APP_PATH . 'Migrations/';

    public const ETC_PATH = self::ROOT_PATH . 'etc/';

    public const VIEWS_PATH = self::APP_PATH . 'Views/';

    public const MODULES_PATH = self::APP_PATH . 'Modules/';

    public const CONTROLLERS_PATH = self::APP_PATH . 'controllers/';

    public const SECURITY_PATH = self::STORAGE_PATH . 'security/';

    public const API_PATH = self::CONTROLLERS_PATH . 'api/';

    public const INTERFACE_PATH = self::APP_PATH . 'interfaces/';

    public const LIBS_PATH = self::APP_PATH . 'libs/';

    public const TRAIT_PATH = self::APP_PATH . 'traits/';

    public const HELPERS_PATH = self::APP_PATH . 'helpers/';

    public const LOCALE_PATH = self::APP_PATH . 'locale/';

    public const MIDDLEWARES_PATH = self::APP_PATH . 'middlewares/';

    public const WIDGETS_PATH = self::APP_PATH . 'widgets/';

    public const PUBLIC_PATH = self::ROOT_PATH . 'public/';

    public const ASSETS_PATH = self::ROOT_PATH . 'assets/';

    public const IMAGES_PATH = self::ASSETS_PATH . 'img/';

    public const CSS_PATH = self::ASSETS_PATH . 'css/';

    public const JS_PATH = self::ASSETS_PATH . 'js/';

    public const SCRIPTS_PATH = self::ROOT_PATH . 'scripts/';

    public const CORE_SCRIPTS_PATH = self::CORE_PATH . 'scripts/';

}


