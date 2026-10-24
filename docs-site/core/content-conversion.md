---
title: Conversión de HTML, XML e imágenes
source:
  - docs/core/content-conversion.md
source_revision: 0cd8a411195c4373648faf320416191e3018ecc3
status: partial
---

# Conversión de HTML, XML e imágenes

`XML`, `HTML`, `Image` y `MediaType` proporcionan helpers de parsing, extracción y conversión. No son un sanitizador de HTML ni una política de validación de entrada.

## XML y DOM

`XML::isValidXML($value, $fast_check)` hace una comprobación simple de delimitadores en modo rápido; el modo normal intenta parsear con SimpleXML. `toArray($xml)` convierte un documento XML a arrays y usa `@attributes` para atributos. `fromArray($array, $root_elem, $header)` requiere el paquete Composer opcional `spatie/array-to-xml`.

`getDocument()` y `getDocumentStrict()` cargan HTML en `DOMDocument` mediante `loadHTML()`; “Strict” no significa validación XML estricta. `getXPath()` y `extractNodes()` consultan nodos o atributos. También existen extracción de texto, serialización sin cabecera y helpers para quitar tags, IDs o atributos.

## HTML

`HTML2Text()` elimina nodos `script` y `style` durante la conversión a texto; `toText()` aplica `strip_tags()` tras decodificar entidades. La clase añade extracción de enlaces, CSS, IDs y artículos, normalización de URL, y eliminación o transformación de tags, atributos y marcadores.

Estas funciones transforman contenido; no garantizan neutralizar todos los vectores XSS ni conservar markup válido. `removeComments()` está marcada como no fiable en el código y no debe usarse como filtro de seguridad.

## Imágenes

`Image::isValidImage($path)` usa `file_exists()` para paths y una petición de headers para URLs; no decodifica la imagen. `convertWebpToJpg()` depende de GD, guarda un JPG temporal y, si falla, retorna `app.default_featured_img`.

`MediaType::getImageType()` extrae el subtipo de `data:image/...;`. `renderImage()` decodifica, envía las cabeceras, imprime los bytes y termina el proceso. No valida contenido real ni aplica un límite de tamaño.

No pases HTML, URLs ni base64 controlados por usuarios suponiendo que estos helpers sustituyen autorización, reglas de red o sanitización. No se probaron entradas adversariales ni salidas HTTP.
