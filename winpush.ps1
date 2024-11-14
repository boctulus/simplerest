# Ejecutar el script de actualización de versión
.\update_version.ps1

# Comprobar si se ha pasado un argumento
if ($args.Count -eq 0) {
    $msg = "Automatic update"
} else {
    $msg = $args -join " "
}

# Ejecutar los comandos de Git
git add *
git commit -m "$msg"
git push
