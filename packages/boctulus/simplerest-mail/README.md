# SimpleRest Mail

`boctulus/simplerest-mail` contains all of SimpleRest's mail code: the
PHPMailer-based SMTP sender, the mailer contract and base class, the audit and
remote WordPress mailers, and the email template helper. It was extracted from
the core so the framework itself requires nothing but PHP.

Classes (namespace `Boctulus\Simplerest\Mail\`, PSR-4 from `src/`):

- `Mail` (SMTP via PHPMailer; extends `MailBase`, implements `IMail`)
- `MailBase`
- `MailAuditor` (writes each send to JSON instead of delivering)
- `MailFromRemoteWP` (sends through a remote WordPress endpoint)
- `EmailTemplate`
- `Interfaces\IMail`

These classes were previously `Boctulus\Simplerest\Core\Libs\...` and
`Boctulus\Simplerest\Core\Interfaces\IMail`; there are no aliases for the old names.
