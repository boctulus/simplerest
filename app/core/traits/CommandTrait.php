<?php

namespace Boctulus\Simplerest\Core\Traits;

trait CommandTrait {
    public function handle($args) {
        if (empty($args)) {
            $this->help(); // Mostrar ayuda si no se proporcionan argumentos
            return;
        }

        $method = array_shift($args);

        if (__CLASS__ === 'HelpCommand'){
            $this->help($method, ...$args);
            return;
        }

        if (!method_exists($this, $method)) {
            dd("Method not found for " . __CLASS__ . "::$method");
            exit;
        }

        // Llamar al método dinámicamente con los argumentos restantes
        call_user_func_array([$this, $method], $args);
    }

    // Método de ayuda por defecto, puede ser sobrescrito por cada comando
    protected function help($name = null, ...$args) {
        dd("Usage: command [method] [args...]");
    }

    /**
     * Parsea opciones de línea de comandos en formato --key=value, --key:value, o --key
     *
     * Soporta:
     *   --limit=100        → ['limit' => '100']
     *   --dry-run          → ['dry_run' => true]
     *   --author:"John Doe" → ['author' => 'John Doe']
     *   --name="Leche"     → ['name' => 'Leche']
     *
     * @param array $args Argumentos del comando
     * @return array Opciones parseadas en formato ['key' => 'value']
     */
    protected function parseOptions(array $args): array
    {
        $options = [];

        foreach ($args as $arg) {
            // Match --key=value or --key:value
            if (preg_match('/^--([^=:]+)[=:](.+)$/', $arg, $matches)) {
                $key = str_replace('-', '_', $matches[1]);
                $value = trim($matches[2], '"\'');
                $options[$key] = $value;
            }
            // Match --key (boolean flag)
            elseif (preg_match('/^--(.+)$/', $arg, $matches)) {
                $key = str_replace('-', '_', $matches[1]);
                $options[$key] = true;
            }
        }

        return $options;
    }

    /**
     * Obtiene una opción parseada con valor por defecto
     *
     * @param array $options Opciones parseadas con parseOptions()
     * @param string $key Nombre de la opción
     * @param mixed $default Valor por defecto si no existe
     * @return mixed
     */
    protected function getOption(array $options, string $key, $default = null)
    {
        return $options[$key] ?? $default;
    }
}