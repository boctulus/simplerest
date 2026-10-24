# Correo opcional

`boctulus/simplerest-mail` reúne adaptadores con efectos diferentes. El manifiesto requiere `phpmailer/phpmailer ^6.9 || ^7.1`; el core no contiene estas clases. Las credenciales y configuración pertenecen al host.

## Adaptadores

| Clase | Comportamiento observado | Efecto y límite |
| --- | --- | --- |
| `Mail` | Usa PHPMailer/SMTP; `send()` recibe destinatarios, asunto, cuerpo, adjuntos, remitente y copias. | Envía un mensaje externo. No se envió correo ni se comprobó un servidor SMTP. |
| `MailAuditor` | Implementa `send()` y `recent()`. | No entrega correo: escribe JSON con destinatarios, asunto, cuerpo, adjuntos y cabeceras de destinatarios bajo `src/logs/mail` del core. Los archivos pueden contener datos personales o contenido sensible; protege acceso y retención. |
| `MailFromRemoteWP` | `setRemote($url)` elige endpoint y `send()` usa `consume_api()` por POST. | Envía información a un WordPress remoto. El código serializa sólo primer destinatario, asunto y cuerpo; no incluye en el payload observado adjuntos, CC, BCC, remitente ni reply-to. La respuesta no fue validada contra el `bool` declarado. |
| `EmailTemplate` | Formatea contenido, renderiza una plantilla y genera listas HTML simples. | Produce contenido local; no envía por sí mismo. |

Las clases bajo `Boctulus\Simplerest\Mail\` son la API actual del paquete. La interfaz `IMail` permite un contrato común en código, pero no hace equivalentes el envío SMTP, el envío remoto y la auditoría a disco.

## Configuración y comprobación

`Mail` obtiene el mailer por defecto desde `Config::get()['email']['mailer_default']`; sus opciones admitidas corresponden a propiedades SMTP de PHPMailer. Mantén los secretos en la configuración protegida del host. El logger/auditor guarda destinatarios y contenido; restringe sus permisos y define retención antes de activarlo.

La inspección fue estática. No se envió correo, no se llamó a un WordPress remoto y no se escribieron logs.
