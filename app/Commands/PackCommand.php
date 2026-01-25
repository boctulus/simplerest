<?php

use Boctulus\Simplerest\Core\Interfaces\ICommand;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Traits\CommandTrait;

/*
    php com pack
    
    Options:
    -s, --source : Source directory (default: D:\laragon\www\simplerest)
    -d, --dest   : Destination directory (default: D:\laragon\www\simplerest-pack)
    
    Examples:
    php com pack
    php com pack -s /custom/source -d /custom/dest
    php com pack --source /custom/source --dest /custom/dest
*/

// Only include the packager class when needed, not on every command load
function includePackager() {
    static $included = false;
    if (!$included) {
        require_once __DIR__ . '/../../scripts/pack_framework.php';
        $included = true;
    }
}

class PackCommand implements ICommand
{
    use CommandTrait;

    private $defaultSource = 'D:\\laragon\\www\\simplerest';
    private $defaultDest = 'D:\\laragon\\www\\simplerest-pack';

    function handle($options = [])
    {
        includePackager();

        $source = $this->getOption($options, ['s', 'source'], $this->defaultSource);
        $dest = $this->getOption($options, ['d', 'dest'], $this->defaultDest);

        echo "Packaging SimpleRest framework...\n";
        echo "Source: $source\n";
        echo "Destination: $dest\n";

        $packager = new SimpleRestPackager($source, $dest);

        if ($packager->run()) {
            echo "Framework packaged successfully!\n";
            return 0; // Success
        } else {
            echo "Error occurred during packaging!\n";
            return 1; // Error
        }
    }

    function help($name = null, ...$args)
    {
        echo "Packager Command - Creates a clean distribution copy of the SimpleRest framework\n\n";
        echo "Usage:\n";
        echo "  php com pack [options]\n\n";
        echo "Options:\n";
        echo "  -s, --source PATH    Source directory (default: {$this->defaultSource})\n";
        echo "  -d, --dest PATH      Destination directory (default: {$this->defaultDest})\n\n";
        echo "Examples:\n";
        echo "  php com pack\n";
        echo "  php com pack -s /custom/source -d /custom/dest\n";
        echo "  php com pack --source /custom/source --dest /custom/dest\n";
    }

    /**
     * Helper method to get option value from command options
     */
    private function getOption($options, $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (isset($options[$key])) {
                return $options[$key];
            }
        }
        return $default;
    }
}