<?php

namespace Boctulus\FriendlyposWeb\Helpers;

/**
 * CreditNoteHelper
 *
 * Helper para construir correctamente el payload de Notas de Crédito (DTE tipo 61)
 * según los requisitos de la API de OpenFactura (Haulmer)
 *
 * @author Pablo Bozzolo (boctulus)
 * @version 1.0.0
 */
class CreditNoteHelper
{
    /**
     * Construye el payload completo para emitir una Nota de Crédito
     *
     * @param array $dteData Datos del DTE (Encabezado, Detalle, Referencia)
     * @param array $options Opciones adicionales
     *   - responseOptions: array - Tipos de respuesta deseados (default: ['PDF', 'FOLIO'])
     *   - customer: array - Datos del cliente (email, fullName)
     *   - externalReference: array - Referencia externa (hyperlinkText, hyperlinkURL)
     *   - origin: string - Origen del documento (default: 'API')
     *
     * @return array Payload completo listo para enviar a la API
     */
    public static function buildPayload(array $dteData, array $options = []): array
    {
        // Validar que sea tipo 61
        $tipoDTE = $dteData['Encabezado']['IdDoc']['TipoDTE'] ?? null;
        if ($tipoDTE !== 61 && $tipoDTE !== '61') {
            throw new \InvalidArgumentException('El TipoDTE debe ser 61 (Nota de Crédito)');
        }

        // Opciones por defecto
        $responseOptions = $options['responseOptions'] ?? ['PDF', 'FOLIO'];
        $origin = $options['origin'] ?? 'API';

        // Construir el payload base
        $payload = [
            'response' => $responseOptions,
            'dte' => $dteData
        ];

        // Agregar customer si está presente
        if (isset($options['customer'])) {
            $payload['customer'] = $options['customer'];
        }

        // Agregar customizePage si está presente
        if (isset($options['externalReference'])) {
            $payload['customizePage'] = [
                'externalReference' => $options['externalReference']
            ];
        }

        // Construir selfService para Nota de Crédito
        // Este es un campo importante para NC según el plugin de WooCommerce
        $selfServiceConfig = self::buildSelfServiceConfig($dteData, $options);
        if (!empty($selfServiceConfig)) {
            $payload['selfService'] = $selfServiceConfig;
        }

        // Agregar custom origin
        $payload['custom'] = [
            'origin' => $origin
        ];

        return $payload;
    }

    /**
     * Construye la configuración de selfService para Nota de Crédito
     *
     * @param array $dteData Datos del DTE
     * @param array $options Opciones
     * @return array Configuración de selfService
     */
    private static function buildSelfServiceConfig(array $dteData, array $options): array
    {
        $config = [];

        // issueBoleta y allowFactura son opcionales para NC
        if (isset($options['issueBoleta'])) {
            $config['issueBoleta'] = (bool)$options['issueBoleta'];
        }

        if (isset($options['allowFactura'])) {
            $config['allowFactura'] = (bool)$options['allowFactura'];
        }

        // documentReference es importante para NC - extrae de Referencias si existe
        if (isset($dteData['Referencia']) && is_array($dteData['Referencia'])) {
            $config['documentReference'] = [];

            foreach ($dteData['Referencia'] as $ref) {
                $docRef = [];

                if (isset($ref['TpoDocRef'])) {
                    $docRef['type'] = (string)$ref['TpoDocRef'];
                }

                if (isset($ref['FolioRef'])) {
                    $docRef['ID'] = (string)$ref['FolioRef'];
                }

                if (isset($ref['FchRef'])) {
                    $docRef['date'] = $ref['FchRef'];
                }

                if (!empty($docRef)) {
                    $config['documentReference'][] = $docRef;
                }
            }
        }

        return $config;
    }

    /**
     * Valida que el DTE tenga la estructura mínima requerida para una Nota de Crédito
     *
     * @param array $dteData Datos del DTE
     * @return array Array con ['valid' => bool, 'errors' => array]
     */
    public static function validate(array $dteData): array
    {
        $errors = [];

        // Validar TipoDTE
        $tipoDTE = $dteData['Encabezado']['IdDoc']['TipoDTE'] ?? null;
        if ($tipoDTE !== 61 && $tipoDTE !== '61') {
            $errors[] = 'TipoDTE debe ser 61 (Nota de Crédito)';
        }

        // Validar Encabezado
        if (!isset($dteData['Encabezado'])) {
            $errors[] = 'Falta Encabezado';
        }

        // Validar IdDoc
        if (!isset($dteData['Encabezado']['IdDoc'])) {
            $errors[] = 'Falta Encabezado->IdDoc';
        } else {
            $idDoc = $dteData['Encabezado']['IdDoc'];

            if (!isset($idDoc['FchEmis'])) {
                $errors[] = 'Falta Encabezado->IdDoc->FchEmis';
            }

            // NOTA: IndNoRebaja es opcional, RazonAnulacion NO va en IdDoc
        }

        // Validar Emisor
        if (!isset($dteData['Encabezado']['Emisor'])) {
            $errors[] = 'Falta Encabezado->Emisor';
        } else {
            $emisor = $dteData['Encabezado']['Emisor'];

            if (!isset($emisor['RUTEmisor'])) {
                $errors[] = 'Falta Encabezado->Emisor->RUTEmisor';
            }
        }

        // Validar Receptor
        if (!isset($dteData['Encabezado']['Receptor'])) {
            $errors[] = 'Falta Encabezado->Receptor';
        } else {
            $receptor = $dteData['Encabezado']['Receptor'];

            if (!isset($receptor['RUTRecep'])) {
                $errors[] = 'Falta Encabezado->Receptor->RUTRecep';
            }
        }

        // Validar Totales
        if (!isset($dteData['Encabezado']['Totales'])) {
            $errors[] = 'Falta Encabezado->Totales';
        } else {
            $totales = $dteData['Encabezado']['Totales'];

            if (!isset($totales['MntTotal'])) {
                $errors[] = 'Falta Encabezado->Totales->MntTotal';
            }
        }

        // Validar Detalle
        if (!isset($dteData['Detalle']) || !is_array($dteData['Detalle']) || empty($dteData['Detalle'])) {
            $errors[] = 'Falta Detalle o está vacío';
        }

        // Validar Referencia (obligatorio para NC)
        if (!isset($dteData['Referencia']) || !is_array($dteData['Referencia']) || empty($dteData['Referencia'])) {
            $errors[] = 'Falta Referencia o está vacío (obligatorio para Nota de Crédito)';
        } else {
            foreach ($dteData['Referencia'] as $idx => $ref) {
                if (!isset($ref['TpoDocRef'])) {
                    $errors[] = "Falta Referencia[$idx]->TpoDocRef";
                }
                if (!isset($ref['FolioRef'])) {
                    $errors[] = "Falta Referencia[$idx]->FolioRef";
                }
                if (!isset($ref['FchRef'])) {
                    $errors[] = "Falta Referencia[$idx]->FchRef";
                }
                if (!isset($ref['CodRef'])) {
                    $errors[] = "Falta Referencia[$idx]->CodRef (1=Anula, 2=Corrige monto, 3=Corrige texto)";
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Crea un DTE de Nota de Crédito a partir de datos simplificados
     *
     * @param array $params Parámetros
     *   - emisor: array (RUTEmisor, RznSoc, GiroEmis, Acteco, DirOrigen, CmnaOrigen)
     *   - receptor: array (RUTRecep, RznSocRecep, GiroRecep, DirRecep, CmnaRecep)
     *   - totales: array (MntNeto, TasaIVA, IVA, MntTotal)
     *   - items: array de items (NmbItem, QtyItem, PrcItem, MontoItem)
     *   - referencia: array (TpoDocRef, FolioRef, FchRef, CodRef, RazonRef)
     *   - indNoRebaja: bool - Si es anulación (no rebaja stock)
     *   - razonAnulacion: string - Razón de la anulación si indNoRebaja=true
     *
     * @return array DTE completo listo para enviar
     */
    public static function createFromParams(array $params): array
    {
        $dteData = [
            'Encabezado' => [
                'IdDoc' => [
                    'TipoDTE' => 61,
                    'Folio' => 0,  // 0 = El servidor asigna el folio automáticamente
                    'FchEmis' => $params['fechaEmision'] ?? date('Y-m-d'),
                ],
                'Emisor' => $params['emisor'],
                'Receptor' => $params['receptor'],
                'Totales' => $params['totales']
            ],
            'Detalle' => [],
            'Referencia' => []
        ];

        // Agregar campos opcionales de IdDoc
        if (isset($params['indNoRebaja']) && $params['indNoRebaja']) {
            $dteData['Encabezado']['IdDoc']['IndNoRebaja'] = 1;
        }

        // NOTA: RazonAnulacion NO va en IdDoc según esquema SII
        // La razón debe ir en Referencia->RazonRef

        // Procesar items
        if (isset($params['items']) && is_array($params['items'])) {
            foreach ($params['items'] as $idx => $item) {
                $dteData['Detalle'][] = [
                    'NroLinDet' => $idx + 1,
                    'NmbItem' => $item['NmbItem'] ?? 'Item',
                    'QtyItem' => $item['QtyItem'] ?? 1,
                    'PrcItem' => $item['PrcItem'] ?? 0,
                    'MontoItem' => $item['MontoItem'] ?? 0
                ];
            }
        }

        // Procesar referencia
        if (isset($params['referencia'])) {
            if (isset($params['referencia']['TpoDocRef'])) {
                // Referencia única
                $dteData['Referencia'][] = [
                    'NroLinRef' => 1,
                    'TpoDocRef' => $params['referencia']['TpoDocRef'],
                    'FolioRef' => $params['referencia']['FolioRef'],
                    'FchRef' => $params['referencia']['FchRef'],
                    'CodRef' => $params['referencia']['CodRef'] ?? 1,
                    'RazonRef' => $params['referencia']['RazonRef'] ?? 'Anulación de documento'
                ];

                // IndGlobal es opcional
                if (isset($params['referencia']['IndGlobal'])) {
                    $dteData['Referencia'][0]['IndGlobal'] = $params['referencia']['IndGlobal'];
                }
            } elseif (is_array($params['referencia'])) {
                // Múltiples referencias
                foreach ($params['referencia'] as $idx => $ref) {
                    $dteData['Referencia'][] = [
                        'NroLinRef' => $idx + 1,
                        'TpoDocRef' => $ref['TpoDocRef'],
                        'FolioRef' => $ref['FolioRef'],
                        'FchRef' => $ref['FchRef'],
                        'CodRef' => $ref['CodRef'] ?? 1,
                        'RazonRef' => $ref['RazonRef'] ?? 'Referencia documento'
                    ];

                    if (isset($ref['IndGlobal'])) {
                        $dteData['Referencia'][$idx]['IndGlobal'] = $ref['IndGlobal'];
                    }
                }
            }
        }

        return $dteData;
    }
}
