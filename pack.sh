#!/usr/bin/env bash
set -euo pipefail

# Ver 2.0

############################################
# CONFIGURACIÓN
############################################

# Compression level:
# 0 = sin compresión (muy rápido)
# 1 = rápida
# 6 = default gzip
# 9 = máxima (lenta)
COMPRESSION_LEVEL=0

############################################
# PARÁMETROS
############################################

OUTPUT_FILE="${1:-}"
ZIPIGNORE_FILE="${2:-.zipignore}"

############################################
# NOMBRE DE ARCHIVO POR DEFECTO
############################################

if [[ -z "$OUTPUT_FILE" ]]; then
    CURRENT_DIR="$(basename "$(pwd)")"
    OUTPUT_FILE="${CURRENT_DIR}.tar.gz"
fi

############################################
# VALIDACIONES
############################################

if [[ ! -f "$ZIPIGNORE_FILE" ]]; then
    echo "Error: El archivo .zipignore no fue encontrado en el directorio actual." >&2
    exit 1
fi

if ! command -v tar >/dev/null 2>&1; then
    echo "Error: tar no está disponible en el sistema." >&2
    exit 1
fi

if ! command -v pwsh >/dev/null 2>&1; then
    echo "Error: pwsh (PowerShell) no está instalado." >&2
    exit 1
fi

############################################
# LEER EXCLUSIONES
############################################

mapfile -t EXCLUSIONS < <(
    grep -vE '^\s*(#|$)' "$ZIPIGNORE_FILE" \
    | sed 's/^[[:space:]]*//;s/[[:space:]]*$//'
)

if [[ ${#EXCLUSIONS[@]} -eq 0 ]]; then
    echo "Error: No se encontraron exclusiones en .zipignore." >&2
    exit 1
fi

EXCLUSIONS_FILE="$(mktemp)"
trap 'rm -f "$EXCLUSIONS_FILE"' EXIT

for pattern in "${EXCLUSIONS[@]}"; do
    echo "$pattern" >> "$EXCLUSIONS_FILE"
done

############################################
# CREAR TAR.GZ
############################################

echo "Creando archivo: $OUTPUT_FILE"
echo "Nivel de compresión: $COMPRESSION_LEVEL"

if [[ "$COMPRESSION_LEVEL" -eq 0 ]]; then
    # Sin compresión real (solo empaquetado)
    tar -cf "$OUTPUT_FILE" \
        --exclude-from="$EXCLUSIONS_FILE" \
        .
else
    # Compresión gzip con nivel configurable
    tar -cf - \
        --exclude-from="$EXCLUSIONS_FILE" \
        . | gzip -"${COMPRESSION_LEVEL}" > "$OUTPUT_FILE"
fi

if [[ ! -f "$OUTPUT_FILE" ]]; then
    echo "Error: Hubo un problema al crear el archivo." >&2
    exit 1
fi

echo "Archivo creado correctamente."

############################################
# OBTENER VERSIÓN
############################################

VERSION="$(pwsh ./update_version.ps1 --get_version | tr -d '\r\n')"

if [[ -z "$VERSION" ]]; then
    echo "Error: No se pudo obtener la versión actual." >&2
    exit 1
fi

############################################
# COPIA VERSIONADA
############################################

RELEASES_DIR="__releases"
mkdir -p "$RELEASES_DIR"

BASENAME="$(basename "$OUTPUT_FILE" .tar.gz)"
VERSIONED_FILE="${RELEASES_DIR}/${BASENAME}_${VERSION}.tar.gz"

echo "Copiando archivo versionado a __releases..."
cp -f "$OUTPUT_FILE" "$VERSIONED_FILE"

if [[ -f "$VERSIONED_FILE" ]]; then
    echo "Archivo versionado creado correctamente:"
    echo "→ $VERSIONED_FILE"
else
    echo "Error: No se pudo copiar el archivo versionado." >&2
    exit 1
fi
