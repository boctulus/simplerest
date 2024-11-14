# Ruta al archivo composer.test
$file = "composer.json"

# Obtener el contenido del archivo
$content = Get-Content $file -Raw

# Buscar la versión actual
$versionMatch = Select-String -InputObject $content -Pattern '"version":\s*"(\d+\.\d+\.\d+)"' -AllMatches

if ($versionMatch.Matches.Count -gt 0) {
    # Extraer la versión actual
    $current_version = $versionMatch.Matches[0].Groups[1].Value
    
    # Separar la versión en segmentos
    $segments = $current_version -split "\."
    
    # Incrementar el último segmento
    $segments[2] = [int]$segments[2] + 1
    
    # Crear nueva versión
    $new_version = $segments -join "."

    # Crear un archivo temporal con el contenido actualizado
    $tempFile = "$file.tmp"
    $content -replace [regex]::Escape("""version"": ""$current_version"""), """version"": ""$new_version""" | 
        Out-File -FilePath $tempFile -Encoding UTF8 -NoNewline
    
    # Verificar que el reemplazo fue exitoso
    $newContent = Get-Content $tempFile -Raw
    if ($newContent -match [regex]::Escape("""version"": ""$new_version""")) {
        Move-Item -Path $tempFile -Destination $file -Force
        Write-Output "Version actualizada de $current_version a $new_version"
    } else {
        Remove-Item $tempFile
        Write-Output "Error: No se pudo actualizar la version."
    }
} else {
    Write-Output "No se encontró una version válida en el archivo."
}