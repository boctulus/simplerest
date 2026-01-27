<?php

namespace Boctulus\Simplerest\Core\Traits;


/**
 * Trait ErrorReporting
 * Proporciona funcionalidad para manejar errores y advertencias en el sistema.
 * Permite añadir errores a una cola y filtrarlos por severidad.
 *
 * @package Boctulus\Simplerest\Modules\AndroidEngine
 * 
 *  Al recuperar errores se pueden usar mascara de bits para filtrar los errores
 *   
 *  Ej:
 *   
 *  $analyzer->getErrors(AndroidCodeAnalyzer::SEVERITY_WARNING | AndroidCodeAnalyzer::SEVERITY_DEBUG)   
 */
trait ErrorReporting
{
    const SEVERITY_ERROR   = 1;   // 0001
    const SEVERITY_WARNING = 2;   // 0010
    const SEVERITY_INFO    = 4;   // 0100
    const SEVERITY_DEBUG   = 8;   // 1000

    // Mapeo de constantes a sus nombres legibles
    protected $severityText = [
        1  => 'ERROR',
        2  => 'WARNING',
        4  => 'INFO',
        8  => 'DEBUG'
    ];

    /**
     * Añade un error/advertencia a la cola.
     *
     * @param string|array $message Mensaje de error o array con información
     * @param int $severity Severidad del error (por defecto SEVERITY_INFO)
     * @return void
     */
    protected function addError($message, $severity = null)
    {
        if ($severity === null) {
            $severity = $this->getInfoLevel();
        }

        if (is_string($message)) {
            $message = [
                'type' => $severity,
                'text' => $message
            ];
        }
        $this->errors[] = $message;
    }

    /**
     * Función auxiliar para convertir una máscara de bits a un string legible.
     *
     * @param int $bitmask Máscara de severidades.
     * @return string Nombres legibles de las severidades.
     */
    protected function getSeverityNames(int $bitmask): string
    {
        $names = [];
        foreach ($this->severityText as $key => $text) {
            if ($bitmask & $key) {
                $names[] = $text;
            }
        }
        // Si solo hay un valor, devolverlo como string; si hay varios, concatenarlos con '|'
        return count($names) === 1 ? $names[0] : implode('|', $names);
    }

    /**
     * Obtiene todos los errores/advertencias acumulados.
     * Si se proporciona una máscara de severidad, devuelve sólo los errores
     * que coincidan con alguno de los tipos indicados.
     *
     * @param int|null $severity Máscara de severidades a filtrar.
     * @return array Lista de errores/advertencias con nombres legibles.
     */
    public function getErrors($severity = null)
    {
        $filtered = $this->errors;
        if ($severity !== null) {
            $filtered = array_filter($this->errors, function ($error) use ($severity) {
                return ($error['type'] & $severity) !== 0;
            });
        }
        // Convertir la severidad numérica a un string legible
        foreach ($filtered as &$error) {
            $error['type'] = self::getSeverityNames($error['type']);
        }
        return $filtered;
    }

    public function getInfoLevel() {
        return self::SEVERITY_INFO;
    }

    public function getWarningLevel() {
        return self::SEVERITY_WARNING;
    }

    public function getErrorLevel() {
        return self::SEVERITY_ERROR;
    }    

    public function getDebugLevel() {
        return self::SEVERITY_DEBUG;
    }
}
