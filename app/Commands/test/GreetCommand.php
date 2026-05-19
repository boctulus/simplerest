<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

/**
 * Stress-test: positional args, named args, options with choices, aliases.
 *
 * php com test greet John
 * php com test greet --name=John --lang=es --formal
 * php com test greet John --lang=en
 * php com test hello John            (alias)
 */
class GreetCommand extends BaseCommand
{
    private const GREETINGS = [
        'es' => ['informal' => 'Hola',      'formal' => 'Buenos días'],
        'en' => ['informal' => 'Hello',     'formal' => 'Good morning'],
        'fr' => ['informal' => 'Salut',     'formal' => 'Bonjour'],
        'pt' => ['informal' => 'Olá',       'formal' => 'Bom dia'],
    ];

    public function __construct()
    {
        $this->command     = 'greet';
        $this->description = 'Saluda a alguien (prueba posicionales, choices y aliases)';
        $this->aliases     = ['hello', 'hi', 'saludar'];
        $this->examples    = [
            'php com test greet John',
            'php com test greet --name=John --lang=es --formal',
            'php com test greet John --lang=en',
            'php com test hello John              (alias)',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['name', 'lang'],
            'flags'    => ['formal'],
            'options'  => [
                'name'   => ['describe' => 'Nombre de la persona (o primer arg posicional)'],
                'lang'   => ['describe' => 'Idioma del saludo', 'default' => 'es'],
                'formal' => ['describe' => 'Usar forma formal del saludo'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        // Accept name as positional arg or --name
        $name   = $this->opt($parsed, 'name') ?? ($parsed['_positional'][0] ?? 'Mundo');
        $lang   = $this->opt($parsed, 'lang', 'es');
        $formal = $this->opt($parsed, 'formal', false);
        $style  = $formal ? 'formal' : 'informal';

        if (!isset(self::GREETINGS[$lang])) {
            echo "⚠ Idioma '{$lang}' no soportado. Idiomas: " . implode(', ', array_keys(self::GREETINGS)) . "\n";
            $lang = 'es';
        }

        $greeting = self::GREETINGS[$lang][$style];
        echo "{$greeting}, {$name}!\n";
    }
}
