# Conversión de HTML, XML e imágenes

[`XML`](../../src/framework/Libs/XML.php), [`HTML`](../../src/framework/Libs/HTML.php), [`Image`](../../src/framework/Libs/Image.php) y [`MediaType`](../../src/framework/Libs/MediaType.php) ofrecen parsing, extracción y conversión puntual. No son una política de validación de entrada ni un sanitizador de HTML.

## XML y DOM

`XML::isValidXML($value, $fast_check)` comprueba delimitadores en modo rápido; el modo normal intenta parsear con SimpleXML. `toArray($xml)` convierte el documento DOM a arrays y representa atributos bajo `@attributes`; si libxml informa errores retorna un array vacío. `fromArray($array, $root_elem, $header)` requiere que Composer tenga instalado `spatie/array-to-xml`; sin esa dependencia lanza una excepción.

Para HTML, `getDocument()` y `getDocumentStrict()` construyen un `DOMDocument`; ambos llaman a `loadHTML()`. El nombre `getDocumentStrict()` no significa que valide XML estricto. `getXPath()` crea XPath y `extractNodes()` consulta nodos o atributos. `getTextFromNodes()`, `getNodesAsString()` y `saveXMLWithoutHeader()` exponen otras formas de recuperar contenido. Las funciones `stripTag()`, `stripTagById()`, `stripTagByAttribute()` y `stripXMLTags()` extraen o quitan fragmentos según selectores o patrones del código.

## Helpers `HTML`

`HTML` hereda de `XML` y añade extracción de IDs/enlaces/CSS/artículos, normalización de URLs, transformación a texto, eliminación de comentarios/tags/atributos y reemplazo de marcadores PHP. `HTML2Text()` elimina nodos `script` y `style` al convertir texto; `toText()` aplica `strip_tags()` tras decodificar entidades.

Las operaciones de extracción y eliminación están destinadas a transformar contenido. No garantizan neutralizar todos los vectores XSS ni preservan necesariamente markup válido. `removeComments()` está descrita en el código como no fiable; no la uses como filtro de seguridad.

## Imágenes y salida inline

`Image::isValidImage($path)` usa `file_exists()` para un path local. Si recibe URL, hace una petición de headers y busca `200` en la primera línea; no decodifica ni valida los bytes de imagen y puede producir tráfico de red. `convertWebpToJpg($path, $quality)` usa GD si `imagecreatefromwebp()` está disponible, escribe un JPG temporal y retorna el path; si falla, retorna `app.default_featured_img`.

`MediaType::getImageType($dataUri)` extrae el subtipo indicado por `data:image/...;`. `renderImage($dataUri)` decodifica, emite `Content-Type`/`Content-Length`, imprime bytes y llama a `exit`. No aplica una lista MIME, validación real de imagen ni límite de tamaño.

No pases HTML, URLs ni datos base64 controlados por usuarios a estos helpers esperando que sustituyan la autorización, las reglas de red o un sanitizador. No se probaron parsing adversarial, solicitudes externas ni salida HTTP.
