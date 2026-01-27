# Changelog - Credit Note Implementation

## v1.0.1 (2026-01-20)

### 🐛 Bug Fix: RazonAnulacion en ubicación incorrecta

**Problema identificado:**
El campo `RazonAnulacion` estaba siendo incluido en `Encabezado->IdDoc`, causando el error:
```json
{
    "error": {
        "message": "Validación de Esquema",
        "code": "OF-08",
        "details": [{
            "field": "RazonAnulacion",
            "issue": "Este elemento no es esperado..."
        }]
    }
}
```

**Causa:**
Según el esquema del SII para DTEs tipo 61 (Nota de Crédito), el campo `RazonAnulacion` **NO es un campo válido** dentro de `IdDoc`.

**Solución:**
- ❌ **Antes:** `RazonAnulacion` en `Encabezado->IdDoc`
- ✅ **Ahora:** La razón de la anulación va en `Referencia->RazonRef`

**Archivos modificados:**
- `packages/boctulus/friendlypos-web/src/Helpers/CreditNoteHelper.php`
  - Removido `RazonAnulacion` de `IdDoc` en método `createFromParams()`
  - Actualizada validación en método `validate()`

- `tests/test_credit_note_emit.php`
  - Actualizado ejemplo para usar solo `indNoRebaja` sin `razonAnulacion`

- `tests/test_credit_note_curl.sh`
  - Removido campo `RazonAnulacion` del payload

- `tests/test_credit_note_curl.ps1`
  - Removido campo `RazonAnulacion` del payload

- `docs/packages/friendlypos-web/examples/credit-note-example.json`
  - Actualizado JSON de ejemplo

- `docs/packages/friendlypos-web/CREDIT-NOTE-GUIDE.md`
  - Actualizada documentación
  - Agregada sección de troubleshooting para este error
  - Aclarado que la razón va en `Referencia->RazonRef`

**Estructura correcta:**
```json
{
  "dteData": {
    "Encabezado": {
      "IdDoc": {
        "TipoDTE": 61,
        "FchEmis": "2026-01-20",
        "IndNoRebaja": 1
      },
      ...
    },
    ...
    "Referencia": [
      {
        "NroLinRef": 1,
        "TpoDocRef": 39,
        "FolioRef": 631563,
        "FchRef": "2026-01-17",
        "CodRef": 1,
        "RazonRef": "Anulación de documento por solicitud del cliente",
        "IndGlobal": 1
      }
    ]
  }
}
```

**Testing:**
- ✅ Testeado con API de OpenFactura (sandbox)
- ✅ Validado contra esquema del SII
- ✅ Scripts de testing actualizados

---

## v1.0.0 (2026-01-20)

### ✨ Implementación inicial

- ✅ Creación de `CreditNoteHelper`
- ✅ Actualización de `DteDataTransformer` para NC
- ✅ Scripts de testing (PHP, Bash, PowerShell)
- ✅ Documentación completa

**Nota:** Esta versión contenía el bug de `RazonAnulacion` corregido en v1.0.1

---

**Autor:** Pablo Bozzolo (boctulus)
