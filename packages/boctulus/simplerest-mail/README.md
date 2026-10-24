# SimpleRest Mail

`boctulus/simplerest-mail` contains the PHPMailer-based SMTP sender and the
email template helper. It was extracted from the core so the framework itself
requires nothing but PHP.

The existing class names are kept:

- `Boctulus\Simplerest\Core\Libs\Mail` (extends the core `MailBase`, implements `IMail`)
- `Boctulus\Simplerest\Core\Libs\EmailTemplate`

`MailBase`, `IMail`, `MailAuditor` and `MailFromRemoteWP` remain in the core
because they have no external dependencies.
