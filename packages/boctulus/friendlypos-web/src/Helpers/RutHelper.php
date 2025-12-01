<?php

namespace Boctulus\FriendlyposWeb\Helpers;

class RutHelper
{
    /**
     * Clean RUT by removing dots (keeps the dash)
     *
     * @param string $rut
     * @return string Clean RUT without dots but with dash
     */
    public static function cleanRut(string $rut): string
    {
        if (empty($rut)) {
            return $rut;
        }

        // Remove only dots, keep the dash
        return str_replace('.', '', $rut);
    }

    /**
     * Format RUT with dots (XX.XXX.XXX-X format)
     *
     * @param string $rut
     * @return string Formatted RUT with dots
     */
    public static function formatRutWithDots(string $rut): string
    {
        $cleanRut = self::cleanRut($rut);
        
        if (strlen($cleanRut) < 2) {
            return $cleanRut;
        }

        $mainNumber = substr($cleanRut, 0, -1);
        $checkDigit = substr($cleanRut, -1);
        
        // Reverse main number to format from right to left
        $reversedMain = strrev($mainNumber);
        $chunks = str_split($reversedMain, 3);
        $formattedMain = strrev(implode('.', $chunks));
        
        return $formattedMain . '-' . $checkDigit;
    }
}