# Ruta base del proyecto
$basePath = "D:\laragon\www\simplerest\app"

# Cambiar al directorio base
Set-Location $basePath

# Verificar si es un repo Git
if (-not (Test-Path (Join-Path $basePath ".git"))) {
    Write-Error "❌ No se encontró un repositorio Git en $basePath"
    exit 1
}

# Obtener todas las carpetas dentro de app (recursivamente)
$dirs = Get-ChildItem -Path $basePath -Directory -Recurse | Sort-Object FullName -Descending

foreach ($dir in $dirs) {
    # Nombre de carpeta actual
    $folder = Split-Path $dir.FullName -Leaf
    $parent = Split-Path $dir.FullName -Parent

    # Nombre temporal
    $tempName = "__$folder__"
    $tempPath = Join-Path $parent $tempName

    # Si ya existe una versión temporal, la salteamos
    if (Test-Path $tempPath) {
        Write-Host "⚠️ Saltando $dir.FullName porque existe $tempPath"
        continue
    }

    # Renombrar carpeta a temporal
    Write-Host "🔄 Renombrando: $folder → $tempName"
    Rename-Item -Path $dir.FullName -NewName $tempName

    # Agregar cambio a Git
    git add -A
}

# Primer commit (intermedio)
Write-Host "🧾 Commit intermedio..."
git commit -m "Rename folders to __temp__ for case fix"

# Segunda pasada: volver a nombres originales con case actual
$dirs = Get-ChildItem -Path $basePath -Directory -Recurse | Sort-Object FullName -Descending

foreach ($dir in $dirs) {
    $folder = Split-Path $dir.FullName -Leaf
    $parent = Split-Path $dir.FullName -Parent

    if ($folder -like "__*__") {
        # Quitar los dobles guiones bajos
        $originalName = $folder -replace "^__|__$", ""
        $originalPath = Join-Path $parent $originalName

        Write-Host "♻️ Restaurando: $folder → $originalName"
        Rename-Item -Path $dir.FullName -NewName $originalName

        git add -A
    }
}

# Commit final
Write-Host "✅ Commit final..."
git commit -m "Restore folder names with correct case"

# Push opcional
Write-Host "📤 Realizando push..."
git push

Write-Host "🎉 Proceso completado correctamente."
