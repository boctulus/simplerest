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

        /*
            En el Servicio de Impuestos Internos (SII) de Chile,
            el código 33 => `Factura Electrónica` (para ventas a empresas y contribuyentes que recuperan IVA)
            el código 39 => `Boleta Electrónica` (para ventas a consumidor final, con IVA incluido en el precio)
            el código 61 => `Nota de Crédito Electrónica` (para anular o corregir documentos)
        */

        // Si es Factura Electrónica (TipoDTE: 33), se ajusta la estructura a la de Boleta Electrónica
        if ($tipoDte == 33) {
            $dteData = self::adjustInvoiceStructure($dteData);
        }

        // Si es Nota de Crédito (TipoDTE: 61) o Nota de Débito (TipoDTE: 56)
        if ($tipoDte == 61 || $tipoDte == 56) {
            $dteData = self::adjustNoteStructure($dteData);
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
     * Adjust the structure for note types (NC: 61, ND: 56)
     *
     * @param array $dteData
     * @return array
     */
    private static function adjustNoteStructure(array $dteData): array
    {
        // Ajustar campos de Emisor para Notas
        if (isset($dteData['Encabezado']['Emisor'])) {
            $emisor = $dteData['Encabezado']['Emisor'];

            // Para NC, asegurar que los nombres de campos sean consistentes
            if (isset($emisor['RznSocEmisor']) && !isset($emisor['RznSoc'])) {
                $emisor['RznSoc'] = $emisor['RznSocEmisor'];
                unset($emisor['RznSocEmisor']);
            }

            if (isset($emisor['GiroEmisor']) && !isset($emisor['GiroEmis'])) {
                $emisor['GiroEmis'] = $emisor['GiroEmisor'];
                unset($emisor['GiroEmisor']);
            }

            // Asegurar Acteco tiene valor
            if (!isset($emisor['Acteco'])) {
                $emisor['Acteco'] = 525130; // Valor por defecto
            }

            $dteData['Encabezado']['Emisor'] = $emisor;
        }

        // Validar que tenga Referencia (obligatorio para Notas)
        if (!isset($dteData['Referencia']) || empty($dteData['Referencia'])) {
            throw new \InvalidArgumentException('Documento debe incluir al menos una Referencia');
        }

        // Ajustar campos de Referencia
        if (isset($dteData['Referencia']) && is_array($dteData['Referencia'])) {
            foreach ($dteData['Referencia'] as $idx => &$ref) {
                // Asegurar que NroLinRef esté presente
                if (!isset($ref['NroLinRef'])) {
                    $ref['NroLinRef'] = $idx + 1;
                }

                // CodRef es obligatorio
                if (!isset($ref['CodRef'])) {
                    $ref['CodRef'] = 1; // 1=Anula, 2=Corrige monto, 3=Corrige texto
                }

                // RazonRef debe estar presente
                if (!isset($ref['RazonRef']) || empty($ref['RazonRef'])) {
                    $ref['RazonRef'] = 'Anulación de documento';
                }
            }
        }

        // Ajustar Totales para NC
        if (isset($dteData['Encabezado']['Totales'])) {
            $totales = $dteData['Encabezado']['Totales'];

            // Asegurar TasaIVA
            if (!isset($totales['TasaIVA'])) {
                $totales['TasaIVA'] = 19; // IVA estándar en Chile
            }

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