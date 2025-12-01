<?php

namespace Boctulus\FriendlyposWeb\Helpers;

use Boctulus\FriendlyposWeb\Helpers\RutHelper;

class DteDataTransformer
{
    /**
     * Transform DTE data to match the required format for the API
     *
     * @param array $dteData
     * @return array
     */
    public static function transform(array $dteData): array
    {
        // Clean RUT fields by removing dots
        $dteData = self::cleanRutFields($dteData);
        
        // Adjust structure for invoice type (TipoDTE: 33)
        $tipoDte = $dteData['Encabezado']['IdDoc']['TipoDTE'] ?? null;
        
        if ($tipoDte == 33) {
            $dteData = self::adjustInvoiceStructure($dteData);
        }
        
        return $dteData;
    }

    /**
     * Remove dots from all RUT fields in the DTE data
     *
     * @param array $data
     * @return array
     */
    private static function cleanRutFields(array $data): array
    {
        // Process recursively to find and clean all RUT fields
        return self::processArray($data, function($value, $key) {
            if (is_string($key) && preg_match('/RUT.*$/', $key)) {
                return RutHelper::cleanRut($value);
            }
            return $value;
        });
    }

    /**
     * Adjust the structure for invoice type (TipoDTE: 33)
     *
     * @param array $dteData
     * @return array
     */
    private static function adjustInvoiceStructure(array $dteData): array
    {
        // Adjust Emisor fields
        if (isset($dteData['Encabezado']['Emisor'])) {
            $emisor = $dteData['Encabezado']['Emisor'];
            
            // Rename some fields for invoices
            $emisorNew = [
                'RUTEmisor' => $emisor['RUTEmisor'] ?? null,
                'RznSoc' => $emisor['RznSocEmisor'] ?? null,
                'GiroEmis' => $emisor['GiroEmisor'] ?? null,
                'Acteco' => $emisor['Acteco'] ?? 525130, // Default value if not present
                'DirOrigen' => $emisor['DirOrigen'] ?? null,
                'CmnaOrigen' => $emisor['CmnaOrigen'] ?? null,
                'CdgSIISucur' => $emisor['CdgSIISucur'] ?? null,
            ];
            
            // Remove null values
            $emisorNew = array_filter($emisorNew, function($value) {
                return $value !== null;
            });
            
            $dteData['Encabezado']['Emisor'] = $emisorNew;
        }
        
        // Adjust Receptor fields
        if (isset($dteData['Encabezado']['Receptor'])) {
            $receptor = $dteData['Encabezado']['Receptor'];
            
            // For invoices, clean RUTRecep (remove dots)
            if (isset($receptor['RUTRecep'])) {
                $receptor['RUTRecep'] = RutHelper::cleanRut($receptor['RUTRecep']);
            }
            
            // Ensure RUTRecep format is correct (without dots)
            if (isset($dteData['Encabezado']['Receptor']['RUTRecep'])) {
                $dteData['Encabezado']['Receptor']['RUTRecep'] = RutHelper::cleanRut($dteData['Encabezado']['Receptor']['RUTRecep']);
            }
            
            // Add default Contacto field if not present
            if (!isset($receptor['Contacto'])) {
                $receptor['Contacto'] = null;
            }
            
            $dteData['Encabezado']['Receptor'] = $receptor;
        }
        
        // Adjust Totales fields for invoices
        if (isset($dteData['Encabezado']['Totales'])) {
            $totales = $dteData['Encabezado']['Totales'];
            
            // Add TasaIVA for invoices
            if (!isset($totales['TasaIVA'])) {
                $totales['TasaIVA'] = $totales['TasaIVA'] ?? '19';  // Default Chilean VAT
            }
            
            // Remove invoice-specific fields that don't belong in invoice format
            unset($totales['TotalPeriodo'], $totales['VlrPagar'], $totales['MntExe']);
            
            $dteData['Encabezado']['Totales'] = $totales;
        }
        
        return $dteData;
    }

    /**
     * Process array recursively with a callback function
     *
     * @param array $data
     * @param callable $callback
     * @return array
     */
    private static function processArray(array $data, callable $callback): array
    {
        $result = [];
        
        foreach ($data as $key => $value) {
            $newValue = $value;
            
            if (is_array($value)) {
                $newValue = self::processArray($value, $callback);
            }
            
            $result[$key] = $callback($newValue, $key);
        }
        
        return $result;
    }
}