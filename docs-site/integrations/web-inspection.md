---

title: "Inspección web"
source:
  - docs/integrations/web-inspection.md
source_revision: 379f2383edbe4978e934195d6a6ed4102651d154
status: partial
---

# Inspección web

`boctulus/simplerest-web-inspection` combina un detector remoto de tecnologías con selectores DOM locales. Requiere `ext-curl`, `symfony/dom-crawler ^6.4` y `symfony/css-selector ^6.4`.

## Detector `CMS_Scanner`

`Scanner::identify($url)` realiza una petición GET mediante `ApiClient`, sigue redirecciones, usa un user agent de Chrome y habilita una caché de 31 días. Si el estado es cero, al menos 400 o el contenido queda vacío, lanza `RuntimeException`. Para CMS conocidos puede devolver un nombre como `WordPress`, `Shopify`, `Magento`, `PrestaShop`, `Squarespace` o `Wix`; en los demás casos devuelve listas heurísticas `frontend` y `backend` basadas en marcadores del HTML y headers. No devuelve una prueba de propiedad ni un resultado de confianza medido.

El detector acepta una URL y sigue redirects. **Inferencia de seguridad a partir del flujo:** si la entrada proviene de un usuario, puede convertir el servidor de la aplicación en cliente de destinos elegidos externamente. Valida esquema y destino, controla DNS/IP y redirects, y aplica política de egreso antes de exponerlo; una allowlist del primer host por sí sola no restringe el destino final tras una redirección. No se pidió ninguna URL durante esta revisión.

`getBootstrapVersion()` está declarado pero lanza `NotImplementedException`. Los resultados de las reglas de marcadores pueden variar con el HTML, cabeceras, caché y cambios de los sitios.

## Selectores locales con `DomCrawler`

`DomCrawler` extiende el crawler de Symfony a partir de markup entregado por el consumidor. Sus helpers consultan un selector CSS con `get()`, `exists()`, `getAttr()`, `getText()`, `getHTML()`, `getAll()`, `getAttributes()`, `getTexts()` y `getLinks()`. Esta clase analiza el contenido que recibe; no necesita la petición remota del `Scanner`.

La detección y la extracción son tareas separadas. Las cadenas extraídas conservan su origen y no deben tratarse como HTML seguro. No se ejecutaron tests del paquete, no se visitó ningún sitio y no se comprobó una instalación limpia.
