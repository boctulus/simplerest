function get_current_version {
    param (
        [string]$file = "composer.test"
    )
    
    $utf8NoBOM = New-Object System.Text.UTF8Encoding $false
    $content = [System.IO.File]::ReadAllText($file, $utf8NoBOM)
    
    $versionMatch = Select-String -InputObject $content -Pattern '"version":\s*"(\d+\.\d+\.\d+)"' -AllMatches
    
    if ($versionMatch.Matches.Count -gt 0) {
        return $versionMatch.Matches[0].Groups[1].Value
    }
    
    return $null
}

function get_next_version {
    param (
        [string]$current_version
    )
    
    if ($current_version) {
        $segments = $current_version -split "\."
        $segments[2] = [int]$segments[2] + 1
        return $segments -join "."
    }
    
    return $null
}

# Procesar argumentos de línea de comandos
if ($args.Count -gt 0) {
    switch ($args[0]) {
        "--get_version" {
            $version = get_current_version
            if ($version) {
                Write-Output $version
            } else {
                Write-Output "No se encontró una versión válida"
            }
            exit
        }
        "--get_next_version" {
            $current = get_current_version
            if ($current) {
                $next = get_next_version $current
                Write-Output $next
            } else {
                Write-Output "No se encontró una versión válida"
            }
            exit
        }
    }
}

# Script principal de actualización
$file = "composer.test"
$utf8NoBOM = New-Object System.Text.UTF8Encoding $false
$content = [System.IO.File]::ReadAllText($file, $utf8NoBOM)

$current_version = get_current_version
if ($current_version) {
    $new_version = get_next_version $current_version
    
    # Crear un archivo temporal con el contenido actualizado
    $tempFile = "$file.tmp"
    $newContent = $content -replace [regex]::Escape("""version"": ""$current_version"""), """version"": ""$new_version"""
    
    # Guardar con UTF8 sin BOM
    [System.IO.File]::WriteAllText($tempFile, $newContent, $utf8NoBOM)
    
    # Verificar que el reemplazo fue exitoso
    $verificationContent = [System.IO.File]::ReadAllText($tempFile, $utf8NoBOM)
    if ($verificationContent -match [regex]::Escape("""version"": ""$new_version""")) {
        Move-Item -Path $tempFile -Destination $file -Force
        Write-Output "Versión actualizada de $current_version a $new_version"
    } else {
        Remove-Item $tempFile
        Write-Output "Error: No se pudo actualizar la versión."
    }
} else {
    Write-Output "No se encontró una versión válida en el archivo."
}