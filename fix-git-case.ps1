# Ruta base del repositorio
$basePath = "D:\laragon\www\simplerest"

Set-Location $basePath

if (-not (Test-Path (Join-Path $basePath ".git"))) {
    Write-Error "No se encontró un repositorio Git en $basePath"
    exit 1
}

# Archivos o carpetas comunes a ignorar
$excludeDirs = @("vendor", "node_modules", ".git")

# Leer .gitignore si existe
$gitignorePath = Join-Path $basePath ".gitignore"
$gitignorePatterns = @()
if (Test-Path $gitignorePath) {
    $gitignorePatterns = Get-Content $gitignorePath | Where-Object {
        $_ -and ($_ -notmatch "^\s*#") # excluir comentarios y líneas vacías
    }
}

function ShouldIgnore($path) {
    $name = Split-Path $path -Leaf

    # Ignorar directorios por nombre base
    if ($excludeDirs -contains $name) { return $true }

    # Ignorar los que empiezan con punto
    if ($name -match "^\.") { return $true }

    # Ignorar rutas que contienen vendor, node_modules o .git
    if ($path -match "\\vendor\\|\\node_modules\\|\\.git\\") { return $true }

    # Ignorar si coincide con patrones simples de .gitignore
    foreach ($pattern in $gitignorePatterns) {
        $pattern = $pattern.Trim()
        if (-not $pattern) { continue }

        # Convertir patrón gitignore a regex básica
        $regex = [Regex]::Escape($pattern) -replace "\\\*", ".*"
        if ($path -match $regex) { return $true }
    }

    return $false
}

# Obtener carpetas válidas para procesar
$dirs = Get-ChildItem -Path $basePath -Directory -Recurse | Where-Object {
    -not (ShouldIgnore $_.FullName)
} | Sort-Object FullName -Descending

foreach ($dir in $dirs) {
    $folder = Split-Path $dir.FullName -Leaf
    $parent = Split-Path $dir.FullName -Parent
    $tempName = "__$folder__"
    $tempPath = Join-Path $parent $tempName

    if (Test-Path $tempPath) {
        Write-Host "Saltando $dir.FullName porque ya existe $tempPath"
        continue
    }

    Write-Host "Renombrando: $folder -> $tempName"
    Rename-Item -Path $dir.FullName -NewName $tempName
    git add -A
}

Write-Host "Haciendo commit intermedio..."
git commit -m "Rename folders to __temp__ for case fix"

# Restaurar nombres originales
$dirs = Get-ChildItem -Path $basePath -Directory -Recurse | Where-Object {
    -not (ShouldIgnore $_.FullName)
} | Sort-Object FullName -Descending

foreach ($dir in $dirs) {
    $folder = Split-Path $dir.FullName -Leaf
    $parent = Split-Path $dir.FullName -Parent

    if ($folder -like "__*__") {
        $originalName = $folder -replace "^__|__$", ""
        $originalPath = Join-Path $parent $originalName
        Write-Host "Restaurando: $folder -> $originalName"
        Rename-Item -Path $dir.FullName -NewName $originalName
        git add -A
    }
}

Write-Host "Commit final..."
git commit -m "Restore folder names with correct case"

Write-Host "Haciendo push..."
git push

Write-Host "Proceso completado correctamente."
