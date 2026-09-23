# Authentication

This reference describes the current framework path. Authentication behavior still depends on the application controller, database schema, configured handlers, and secrets. The claim-level evidence and limits are recorded in the [audit ledger](../audit/authentication-acl.md).

## Request routing and credential checks

The default <code>front_behaviors</code> configuration assigns request parsing, API route resolution, authentication-route resolution, output, middleware, and error handlers. <code>RequestHandler</code> identifies the versioned auth path; <code>AuthHandler</code> selects an action on the application class <code>MyAuthController</code>. <code>AuthHandler</code> is a route resolver, not a credential guard.

The framework's built-in API resource base is <code>ResourceController</code>. Its constructor calls <code>auth()-&gt;check()</code> before the <code>ApiController</code> constructor builds ACL callables. Ordinary application controllers do not inherit this check automatically. The default <code>FrontController</code> invokes the action before running its configured middleware handler, so that hook is not a pre-action authentication guard.

With <code>remove_api_slug</code> false, the source path is <code>/api/{version}/auth/{action}</code>. With it enabled, the <code>api</code> segment is omitted and the version remains. The separate web routes in <code>config/routes/auth.php</code> declare GET pages at <code>/auth/login</code> and <code>/auth/rememberme</code>; those are not the API auth actions.

## Credential detection and validation

| Credential | Detection source | Validation and identity source |
| --- | --- | --- |
| JWT bearer token | <code>Authorization</code> header, or the <code>token</code> query parameter (converted to a <code>Bearer</code> value) | <code>AuthController::check()</code> decodes with the configured access-token key and algorithm, then checks required claims, expiry, and enabled IP/user-agent restrictions. |
| API key | <code>X-API-KEY</code> header, or the <code>api_key</code> query parameter | <code>AuthController::check()</code> looks up the key in <code>api_keys</code>, obtains its user ID, and loads that user's role and permissions. |

If both credential types are present, <code>Request::authMethod()</code> selects API key first. <code>Request::isAuthenticated()</code> only reports whether either credential was detected on a non-CLI request; it does not prove that the credential is valid. Code that needs validated identity uses the <code>IAuth::check()</code> path.

When no credential is detected, <code>AuthController::check()</code> assigns the configured guest role, a null user ID, and empty user-specific permissions. That is an unauthenticated identity, not an automatic denial: authorization depends on the guest role's ACL rules.

## User, role, and permission representation

<code>auth()</code> returns the <code>IAuth</code> provider. In this repository, <code>Factory::auth()</code> constructs the application class <code>MyAuthController</code>; <code>ResourceController</code> also permits an <code>IAuth</code> implementation to be injected. The interface exposes <code>check()</code>, <code>uid()</code>, <code>getRoles()</code>, <code>getPermissions()</code>, <code>isGuest()</code>, and <code>isRegistered()</code>.

On successful password login, the core controller reads the configured users table and the field names declared by the user model. It verifies the password hash, checks the active state when that column exists, resolves roles, and loads user-specific table and special permissions. The access-token payload carries the user ID, roles, permissions, active state, and database access list. The refresh token is separately signed and contains the user ID rather than the role and permission snapshot.

For API-key requests, roles and permissions are loaded from the database after the key maps to a user. For JWT requests, the token payload supplies the roles and permissions; if the users table has the configured role field, the role ID is mapped through the ACL. When roles are absent for a registered account, the configured registered role is used. <code>uid()</code>, <code>getRoles()</code>, and <code>getPermissions()</code> expose the checked request state; the legacy <code>getCurrentUser()</code> and <code>getCurrentUserId()</code> examples are not part of the current <code>IAuth</code> interface or core controller.

## Login, renewal, and source-level status codes

<code>AuthController::login()</code> issues both access and refresh JWTs after checking the credentials. Renewal is a separate <code>token()</code> action that accepts a refresh token; the access-token check rejects an invalid or expired token and does not silently refresh it.

The controller calls the response error path with HTTP 401 for incorrect login credentials, invalid API keys, and invalid or expired JWTs. Disabled or pending accounts use HTTP 403 in the relevant login/check paths. The automatic API callable gate also uses 403 when an action is not in the controller's callable list.

Local raw-TCP HTTP probes observed 400 for `Authorization: Basic dGVzdA==`, with body `{"status":400,"error":{"type":null,"code":null,"message":"Authorization jwt token not found","detail":null,"location":null}}`; 401 for `Authorization: Bearer invalid.token.value`, with body `{"status":401,"error":{"type":null,"code":null,"message":"Malformed UTF-8 characters","detail":null,"location":null}}`; and 403 for a synthetic API controller whose callable list was empty, with body `{"status":403,"error":{"type":null,"code":null,"message":"Not authorized for Boctulus\\Simplerest\\Controllers\\api\\AuditAclDenyProbe:get","detail":null,"location":null}}`. These results establish the local code paths and those sample bodies, not all malformed-token responses or a DB-backed ACL denial. The PHP built-in server received HTTP/1.1 requests, but SimpleRest emitted status lines beginning `HTTP/2`; bundled `curl.exe` rejected those responses as an unsupported HTTP version. See the open [HTTP response issue](../issues/http-response-uses-invalid-http2-status-line.md).

## Current configuration and application integrations

<code>config/config.php</code> names the users table and reads the access, refresh, and email-token secrets from environment variables. User field names are read from the configured user model; the legacy <code>users_password_field</code>, <code>users_email_field</code>, and <code>users_username_field</code> configuration example is not present in the current configuration.

This checkout sets the access-token lifetime to <code>60 * 15 * 50000</code> seconds (45,000,000 seconds, about 521 days), the refresh-token lifetime to 315,360,000 seconds, and the email-token lifetime to 3,600 seconds. These are current source values, not recommendations or runtime-verified effective settings.

The secure <code>pwdv</code> password-reset flow in this checkout is implemented by <code>app/Controllers/MyAuthController.php</code>, an application override. It is not the generic core <code>AuthController</code> contract. The current app also contains Google/Facebook configuration and application controllers; their presence does not establish a core OAuth login integration.

The core controller depends on the application-provided <code>MyAuthController</code> class selected by <code>AuthHandler</code> and <code>Factory</code>. A standalone consumer must supply compatible application wiring; this repository's application configuration is not a framework default.

## Evidence and verification boundary

- [Request credential detection](../../src/framework/Request.php#L254)
- [Auth route parsing](../../src/framework/Handlers/RequestHandler.php#L74) and [action resolution](../../src/framework/Handlers/AuthHandler.php#L18)
- [Auth provider factory](../../src/framework/Libs/Factory.php#L20) and [IAuth contract](../../src/framework/Interfaces/IAuth.php#L5)
- [ResourceController check](../../src/framework/Api/ResourceController.php#L36), [FrontController order](../../src/framework/FrontController.php#L109), and [core auth check](../../src/framework/Api/AuthController.php#L806)
- [Users table setting](../../config/config.php#L128) and [token settings](../../config/config.php#L173)
- Existing test sources include [HTTP auth tests](../../unit-tests/auth/AuthTest.php#L180) and application-specific [password-reset tests](../../unit-tests/auth/PasswordResetTest.php#L70). They were inspected but not run in this focused runtime phase. No successful login, valid API key/JWT request, or database-backed authorization request was performed.
