# Helpers heredados de host

Esta página registra dos helpers de alcance estrecho que no comparten una API; se agrupan aquí porque sus nombres históricos pueden sugerir garantías que el código no ofrece.

## Identificador de máquina

`boctulus/simplerest-machine-id` aporta `Hardware::UniqueMachineID($salt = '')`. En Windows invoca `diskpart` y utiliza un archivo temporal; en otros sistemas ejecuta `blkid`, con una alternativa basada en `HTTP_HOST` ante una salida que contiene `blkid`. Devuelve un MD5 calculado sobre el resultado y el salt.

El identificador depende del sistema y los permisos de comandos. No es secreto criptográfico, identidad estable garantizada ni credencial adecuada para autenticar un equipo. La llamada a comandos del host no se ejecutó.

## Ofuscación reversible

`boctulus/simplerest-string-obfuscation` conserva `SimpleCrypt`, cuyo algoritmo reordena caracteres con padding y puede revertir la transformación. No es cifrado. No protege contraseñas, tokens, información personal ni datos confidenciales.
