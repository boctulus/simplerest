# JWT authentication

SimpleRest core defines the [`IAuth` contract](../../src/framework/Interfaces/IAuth.php) and detects bearer credentials in [`Request`](../../src/framework/Request.php). The JWT controller used by this application comes from the optional [`simplerest-jwt-auth` package](../../packages/boctulus/simplerest-jwt-auth/src/AuthController.php); `AuthHandler` resolves API auth actions to the application's `MyAuthController`. See the broader [authentication reference](authentication.md) for API-key behavior, guest requests, and the application override.

## Token settings

The checked-in `config/config.php` defines separate settings for access, refresh, and email tokens. Secret values are loaded through `Env`; this reference does not expose any local values.

| Token | Secret environment key | Default algorithm | Checked-in lifetime |
| --- | --- | --- | --- |
| Access | `TOKENS_ACCSS_SECRET_KEY` | `HS256` | 3,600 seconds |
| Refresh | `TOKENS_REFSH_SECRET_KEY` | `HS256` | 1,209,600 seconds |
| Email | `TOKENS_EMAIL_SECRET_KEY` | `HS256` | 3,600 seconds |

These are current checkout settings, not universal defaults. Configure distinct, strong secrets outside source control. The package encodes `iat`, `exp`, request IP, and user agent in tokens; the access and refresh flows use separate secret/algorithm configuration. IP and user-agent rejection depends on the corresponding config switches.

## Login and renewal

`POST /api/v1/auth/login` checks the configured user model's login/password fields and verifies the password hash. For an accepted active account, the controller returns an access JWT and a refresh JWT. The access payload includes the UID, roles, table/special permissions, account state, and application-provided DB access; the refresh payload contains the UID and common token metadata rather than the permission arrays.

The response shape is source-derived:

```json
{
  "access_token": "<signed access token>",
  "token_type": "bearer",
  "expires_in": 3600,
  "refresh_token": "<signed refresh token>",
  "roles": [],
  "uid": 0,
  "db_access": []
}
```

`POST /api/v1/auth/token` accepts the refresh JWT in `Authorization: Bearer ...`. It validates the token, reloads the account/role/permission state where applicable, and returns a new access token. This action does not return a replacement refresh token in the inspected source. Access-token validation does not silently refresh an expired token.

## Credential detection and related actions

`Request::getAuth()` prefers a non-empty `token` query parameter and converts it to a bearer value; otherwise it reads the Authorization header variants. The query form is implemented for compatibility, but URLs can be retained in browser history, logs, and referrers. Prefer the Authorization header for clients. When both an API key and JWT are present, `authMethod()` selects the API key path first.

The package controller also defines registration, remember/email-token, password-change, and impersonation actions. The current app's secure `pwdv` password-reset flow is an override in `MyAuthController`, not a core guarantee. Impersonation actions require the configured ACL capability and follow their own refresh-token path; see [ACL authorization](acl.md) and the [application auth controller](../../app/Controllers/MyAuthController.php).

## Verification boundary

The package manifest requires `firebase/php-jwt`; package presence and source inspection do not prove a clean consumer installation. The authentication test source was inspected, not run. No login, renewal, email-token, or impersonation request was performed, and no database-backed permissions or secrets were read for this guide.

Sources: [`AuthController`](../../packages/boctulus/simplerest-jwt-auth/src/AuthController.php), [`AuthHandler`](../../src/framework/Handlers/AuthHandler.php), [`Request`](../../src/framework/Request.php), [`IAuth`](../../src/framework/Interfaces/IAuth.php), and [`token settings`](../../config/config.php).
