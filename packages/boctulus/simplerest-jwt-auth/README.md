# SimpleRest JWT Auth

`boctulus/simplerest-jwt-auth` contains the JWT-based authentication
controller (login, token refresh, registration, email verification and
password reset tokens). It requires `firebase/php-jwt`, the PHP JWT library
(it does not use the Firebase platform). It was extracted from the core so the
framework itself does not carry this package's dependencies.

The existing class name is kept: `Boctulus\Simplerest\Core\Api\AuthController`.
It implements the core `IAuth` interface, which remains in the core;
applications extend it (for example `Boctulus\Simplerest\Controllers\MyAuthController`).
