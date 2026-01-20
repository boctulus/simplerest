#!/usr/bin/env bash
set -euo pipefail

OUTPUT_FILE="${1:-}"
ZIPIGNORE_FILE="${2:-.zipignore}"

# Si no se especifica OUTPUT_FILE, usar el nombre del directorio actual
if [[ -z "$OUTPUT_FILE" ]]; then
    CURRENT_DIR="$(basename "$(pwd)")"
    OUTPUT_FILE="${CURRENT_DIR}.tar.gz"
fi

# Validar .zipignore
if [[ ! -f "$ZIPIGNORE_FILE" ]]; then
    echo "Error: El archivo .zipignore no fue encontrado en el directorio actual." >&2
    exit 1
fi

# Leer exclusiones (ignorar líneas vacías y comentarios)
mapfile -t EXCLUSIONS < <(
    grep -vE '^\s*(#|$)' "$ZIPIGNORE_FILE" | sed 's/^[[:space:]]*//;s/[[:space:]]*$//'
)

if [[ ${#EXCLUSIONS[@]} -eq 0 ]]; then
    echo "Error: No se encontraron exclusiones en .zipignore." >&2
    exit 1
fi

# Verificar tar
if ! command -v tar >/dev/null 2>&1; then
    echo "Error: tar no está disponible en el sistema." >&2
    exit 1
fi

# Crear archivo temporal con exclusiones para tar
EXCLUSIONS_FILE="$(mktemp)"
trap 'rm -f "$EXCLUSIONS_FILE"' EXIT

for pattern in "${EXCLUSIONS[@]}"; do
    echo "$pattern" >> "$EXCLUSIONS_FILE"
done

echo "Creando el archivo TAR.GZ con exclusiones..."
tar -czf "$OUTPUT_FILE" \
    --exclude-from="$EXCLUSIONS_FILE" \
    .

if [[ ! -f "$OUTPUT_FILE" ]]; then
    echo "Error: Hubo un problema al crear el archivo TAR.GZ." >&2
    exit 1
fi

echo "Archivo creado correctamente: $OUTPUT_FILE"

# Obtener versión desde update_version.ps1
if ! command -v pwsh >/dev/null 2>&1; then
    echo "Error: pwsh (PowerShell) no está instalado." >&2
    exit 1
fi

VERSION="$(pwsh ./update_version.ps1 --get_version | tr -d '\r\n')"

if [[ -z "$VERSION" ]]; then
    echo "Error: No se pudo obtener la versión actual." >&2
    exit 1
fi

# Crear carpeta __releases si no existe
RELEASES_DIR="__releases"
mkdir -p "$RELEASES_DIR"

# Generar nombre versionado
BASENAME="$(basename "$OUTPUT_FILE" .tar.gz)"
VERSIONED_FILE="${RELEASES_DIR}/${BASENAME}_${VERSION}.tar.gz"

echo "Copiando el archivo versionado a __releases..."
cp -f "$OUTPUT_FILE" "$VERSIONED_FILE"

if [[ -f "$VERSIONED_FILE" ]]; then
    echo "Archivo versionado creado correctamente: $VERSIONED_FILE"
else
    echo "Error: No se pudo copiar el archivo versionado." >&2
    exit 1
fi
