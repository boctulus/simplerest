# SimpleRest Brevo

Envío transaccional mediante el SDK oficial `getbrevo/brevo-php` 5.x.

```php
use Boctulus\Simplerest\Core\Libs\Brevo;

$sent = Brevo::send('recipient@example.com', 'Asunto', '<p>Contenido</p>');
if (!$sent) {
    throw new RuntimeException(Brevo::errors());
}
$messageId = Brevo::messageId();
```

Instala el paquete y sus dependencias con Composer. En este monorepo usa
`COMPOSER=composer-dev.json composer install` (en PowerShell establece primero
`$env:COMPOSER = 'composer-dev.json'`). El autoload de desarrollo incluye `src/`.

Configura `brevo_api_key` con `BREVO_API_KEY`; se conserva `SENDINBLUE_API_KEY`
como alternativa. El remitente utiliza `email.from.address` y `email.from.name`.
La clave API es diferente de la contraseña SMTP. El remitente debe estar
autorizado en Brevo y las restricciones de IP deben permitir el equipo emisor.

`send()` admite destinatarios con `email` y `name`, listas, CC, BCC, Reply-To,
HTML y texto alternativo. Los adjuntos admiten rutas locales, `['path' => ...,
'name' => ...]` o `['data' => ..., 'name' => ...]`. Devuelve `false` y conserva
el error en `errors()` cuando el envío falla. Un resultado `true` indica que
Brevo aceptó la solicitud y devolvió un ID; la entrega se consulta en Brevo.

Para SMTP utiliza `Boctulus\Simplerest\Mail\Mail`, con
`Mail::setMailer('brevo')` y el perfil `email.mailers.brevo`. El perfil está
seleccionado por defecto en la configuración local del monorepo.

Si PHP necesita un almacén de certificados, configura `MAIL_CA_BUNDLE` con una
ruta PEM válida. La API usa `email.ca_bundle`; el perfil SMTP utiliza la misma
variable mediante `SMTPOptions.ssl.cafile`, manteniendo la validación TLS.

Las siguientes pruebas envían **un correo real** a `boctulus@gmail.com`:

```powershell
php scripts/test-brevo.php api
php scripts/test-brevo.php smtp
```

Para diagnóstico SMTP filtrado, agrega `--diagnose`. Las pruebas sin red están
en `unit-tests/mail/BrevoTest.php` y se ejecutan con
`php vendor/phpunit/phpunit/phpunit unit-tests/mail`.

Referencia: [SDK PHP oficial de Brevo](https://developers.brevo.com/guides/php).

