# Ruta al archivo index.php
$file = "index.php"

# Obtener la versión actual desde el archivo
$current_version = (Get-Content $file | Select-String -Pattern "Version: (\d+\.\d+\.\d+|\d+\.\d+|\d+)" | ForEach-Object { $_.Matches.Groups[1].Value })

# Separar la versión en partes (segmentos)
$segments = $current_version -split "\."

# Incrementar el último segmento de la versión
if ($segments.Length -eq 3) {
    $segments[2] = [int]$segments[2] + 1  # Incrementa el patch (Z) en X.Y.Z
} elseif ($segments.Length -eq 2) {
    $segments[1] = [int]$segments[1] + 1  # Incrementa el minor (Y) en X.Y
} else {
    $segments[0] = [int]$segments[0] + 1  # Incrementa el major (X)
}

# Combinar los segmentos de nuevo en la nueva versión
$new_version = ($segments -join ".")

# Reemplazar la línea de versión en el archivo
(Get-Content $file) -replace "Version: $current_version", "Version: $new_version" | Set-Content $file
