#!/bin/bash

# Test: Emisión de Nota de Crédito (DTE tipo 61) usando CURL
#
# Este script prueba la emisión de una Nota de Crédito directamente
# contra el endpoint REST del controlador OpenFacturaController
#
# Ejecutar con: bash tests/test_credit_note_curl.sh
#
# @author Pablo Bozzolo (boctulus)

echo ""
echo "========================================"
echo "TEST: Emisión de Nota de Crédito con CURL"
echo "========================================"
echo ""

# Leer .env para determinar modo
if [ -f "$(dirname "$0")/../.env" ]; then
    source "$(dirname "$0")/../.env"
fi

# Determinar modo sandbox
SANDBOX="${OPENFACTURA_SANDBOX:-true}"

# Seleccionar API key según modo
if [ "$SANDBOX" = "true" ]; then
    API_KEY="${OPENFACTURA_API_KEY_DEV}"
    MODE="SANDBOX (Desarrollo)"
else
    API_KEY="${OPENFACTURA_API_KEY_PROD}"
    MODE="PRODUCCIÓN"
fi

BASE_URL="${APP_URL:-http://simplerest.lan}"
ENDPOINT="$BASE_URL/api/v1/openfactura/dte/emit"

echo "Configuración:"
echo "  - Modo: $MODE"
echo "  - Base URL: $BASE_URL"
echo "  - API Key: ${API_KEY:0:10}..."
echo "  - Endpoint: $ENDPOINT"
echo "  - OPENFACTURA_SANDBOX: $SANDBOX"
echo ""

# Construir el payload JSON
# IMPORTANTE: Este payload usa la estructura CORRECTA para Notas de Crédito
read -r -d '' PAYLOAD << 'EOF'
{
  "dteData": {
    "Encabezado": {
      "IdDoc": {
        "TipoDTE": 61,
        "Folio": 0,
        "FchEmis": "2026-01-20",
        "IndNoRebaja": 1
      },
      "Emisor": {
        "RUTEmisor": "76795561-8",
        "RznSoc": "HAULMER SPA",
        "GiroEmis": "COMERCIO",
        "Acteco": 479110,
        "DirOrigen": "Pasaje Los Pajaritos 8357",
        "CmnaOrigen": "Santiago"
      },
      "Receptor": {
        "RUTRecep": "76795561-8",
        "RznSocRecep": "HAULMER SPA",
        "GiroRecep": "COMERCIO",
        "DirRecep": "Pasaje Los Pajaritos 8357",
        "CmnaRecep": "Santiago"
      },
      "Totales": {
        "MntNeto": 84,
        "TasaIVA": 19,
        "IVA": 16,
        "MntTotal": 100
      }
    },
    "Detalle": [
      {
        "NroLinDet": 1,
        "NmbItem": "AAAA",
        "QtyItem": 1,
        "PrcItem": 84,
        "MontoItem": 84
      }
    ],
    "Referencia": [
      {
        "NroLinRef": 1,
        "TpoDocRef": 39,
        "FolioRef": 631563,
        "FchRef": "2026-01-17",
        "CodRef": 1,
        "RazonRef": "Alguna referencia",
        "IndGlobal": 1
      }
    ]
  },
  "responseOptions": ["PDF", "FOLIO", "TIMBRE"]
}
EOF

echo "Enviando petición..."
echo ""

# Hacer la petición con CURL
curl -X POST "$ENDPOINT" \
  -H "Content-Type: application/json" \
  -H "X-Openfactura-Api-Key: $API_KEY" \
  -H "X-Openfactura-Sandbox: $SANDBOX" \
  -d "$PAYLOAD" \
  -w "\n\nHTTP Status: %{http_code}\n" \
  -s | jq '.'

echo ""
echo "========================================"
echo "Test finalizado"
echo "========================================"
echo ""
