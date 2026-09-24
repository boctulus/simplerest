# ACL and API authorization

This page covers the core ACL mechanism and its use by built-in resource API controllers. The active rules and concrete ACL implementation are application-controlled. See the [claim ledger](../audit/authentication-acl.md) for rejected, application-specific, and unresolved legacy claims.

## Rule source and runtime selection

<code>Factory::acl()</code> includes <code>config/acl.php</code>; it does not select a concrete ACL implementation on its own. The file must return an implementation of the core ACL contract. The base <code>Core\Security\Acl</code> is abstract.

The core builder supports named roles, role IDs, a configured guest and registered role, inherited permissions, resource permissions, special permissions, and explicit role denies. <code>addInherit()</code> records a parent and copies the parent's permission arrays at declaration time; the role hierarchy service also uses the recorded lineage for role comparisons.

Permission sources do not form one universal <code>DENY &gt; USER_GRANT &gt; ROLE_GRANT</code> ordering. The compiled path combines role grants, adds per-user special permissions, replaces the role-derived table grant for a resource when a per-user table-permission bitmask is supplied, and then applies explicit denies. Explicit deny wins for the matching permission. A per-user special grant is additive to role special grants. This describes source compilation, not a runtime policy probe; see the ledger for the older command guide's broader claim.

Resource permissions are table/resource actions: <code>show</code>, <code>show_all</code>, <code>list</code>, <code>list_all</code>, <code>create</code>, <code>update</code>, and <code>delete</code>. The builder expands <code>read</code> to <code>show</code> plus <code>list</code>, and <code>write</code> to <code>create</code>, <code>update</code>, and <code>delete</code>. Special permissions are named capabilities such as <code>read_all</code>, <code>write_all</code>, <code>grant</code>, <code>impersonate</code>, <code>fill_all</code>, and <code>transfer</code>. The valid special-permission list can be supplied by an ACL subclass.

This repository's <code>config/acl.php</code> selects the optional <code>FineGrainedACL</code> package, reads role and special-permission names from the database, declares application roles, and writes a serialized ACL file. Its local <code>$acl_cache</code> value is false, so that configuration rebuilds and writes the object rather than restoring it from the file. The <code>BasicACL</code> and <code>FineGrainedACL</code> package directories are optional integrations; the core factory does not auto-discover or bind them.

The core auth controller resolves roles from a user role field when present, otherwise from <code>user_roles</code> and <code>roles</code>. Login and API-key validation load <code>user_tb_permissions</code> and <code>user_sp_permissions</code>; JWT requests carry those permissions in the access-token payload. The <code>FineGrainedACL</code> subclass additionally loads explicit user-deny rows. This checkout's <code>config/acl.php</code> explicitly grants <code>read_all</code> and <code>write_all</code> to the guest role. The core ACL does not supply those guest grants as defaults; the ACL engine test fixture also verifies a guest with no global <code>read_all</code>. The application database's effective special-permission catalog and current runtime result were not queried.

## Automatic resource API authorization

The built-in <code>ApiController</code> path performs these steps:

1. <code>ResourceController</code> calls <code>auth()-&gt;check()</code> during controller construction.
2. <code>ApiController</code> resolves the resource table and asks the ACL for permissions associated with the current identity.
3. It adds allowed action names to the controller callable list.
4. <code>FrontController</code> constructs the controller and then checks that the resolved API method is in that list. A failed check sends 403 before invoking the action method.

The gate runs after controller construction, so the controller constructor itself has run. The check applies to versioned non-auth API dispatch. Auth actions skip this callable gate, and ordinary web controllers have no global ACL guard. A custom API controller that does not use the built-in <code>ResourceController</code>/<code>ApiController</code> path must provide its own credential and authorization behavior; the FrontController callable check only compares method names. A local synthetic controller with an empty callable list returned a 403 at this gate, but no DB-backed automatic-resource denial was run. The built-in <code>ApiController</code> also unconditionally calls two impersonation classes that are not defined or Composer-loadable in this repository; see the open [controller dependency issue](../issues/api-controller-missing-impersonation-types.md).

For the ordinary permission path, GET is enabled by <code>read_all</code> or the resource's read actions, POST by <code>write_all</code> or <code>create</code>, PUT/PATCH by <code>write_all</code> or <code>update</code>, and DELETE by <code>write_all</code> or <code>delete</code>. The controller uses one <code>get</code> callable for list and item retrieval.

Missing credentials become the guest role, not an immediate 401. If that role has no matching action, the callable gate responds with 403. Invalid credentials are rejected earlier by the auth check with 401. The current application config declares <code>read_all</code> and <code>write_all</code> for <code>guest</code>; this is application-specific and the optional ACL integration validates special permissions against database rows. No runtime request confirmed its effective result.

## Wildcards, fallback, and API versions

The engine denies an unlisted permission by default. Unknown roles do not contribute allows. <code>read_all</code> and <code>write_all</code> are explicit global capabilities; the compiler expands them to read or write actions across resources. This is not an implicit allow for every role.

Literal <code>*</code> resource allows are reserved syntax and rejected by <code>Acl::addResourcePermissions()</code>, role compilation, per-user compilation, and per-user table masks. The compiled <code>allow['*']</code> bucket is reserved for the expansion of explicit <code>read_all</code>/<code>write_all</code> capabilities; use those capabilities for cross-resource grants. Explicit wildcard denies remain supported and are checked by both compiled lookup paths. Focused tests cover these contracts; see the resolved [wildcard behavior issue](../issues/acl-literal-wildcard-path-divergence.md).

<code>ApiHandler</code> validates and retains the route version, but resolves the resource controller from the resource slug. <code>ApiController</code> keys resource permissions by table name and has no separate version key. Application controller code may still branch on the API version; that does not make the ACL version-specific.

## Observed authorization discrepancies

- Focused tests exercised the real <code>ApiController</code> constructor and <code>ApiHandler</code> method resolution with test doubles for authentication, ACL, model access, and the absent impersonation integration. With a per-user mask, POST's create bit still adds <code>get</code> while dispatch resolves <code>post</code>, so an otherwise ungranted POST is rejected by the exact callable gate. PATCH's update bit formerly added <code>putch</code>; that spelling was corrected to <code>patch</code> in this audit and the focused test now confirms the callable matches dispatch. Git history shows both earlier strings originated in the 2020 per-user mask branch; no caller or test established either unusual spelling as intentional. See the open [per-user mask issue](../issues/api-per-user-acl-callables.md).
- When a non-null per-user table mask is supplied, the controller appends its callables without clearing role-derived callables. A focused test confirms a role's POST callable remains when the user mask is zero, despite the nearby source comment describing replacement. Desired policy/intent remains unresolved.
- <code>Request::method()</code> honors configured URL/header method overrides, and <code>ApiHandler</code> dispatches using that effective method, while <code>ApiController</code> selects ACL callables from raw <code>$_SERVER['REQUEST_METHOD']</code>. Focused tests verified both raw POST overridden to PATCH and raw PATCH overridden to POST dispatch a method absent from the ACL callable set. No live DB-backed override request was performed; see the open [method override issue](../issues/api-method-override-acl-mismatch.md).

The method-override and per-user-mask findings are recorded for framework follow-up. Their dispatch mismatches are reproduced by focused source-path tests; the intended policy behind role/mask precedence remains unresolved. The PATCH token correction was the only framework code change in this audit and followed the user's explicit exception.

## Record ownership and HTTP status boundary

<code>ApiController</code> has a default owner-scope path for <code>belongs_to</code> resources, and folder access is handled by separate folder-aware code. Those mechanisms do not establish generic row-level or attribute-level ACL rules. The legacy claims about generic row- and field-level ACL are rejected as current framework contracts.

The FrontController passes 403 to its error response when a resolved API action is not callable. A local HTTP probe with a synthetic empty-callable controller observed a 403 body naming that controller and method. This does not establish end-to-end ACL integration. The response used a textual <code>HTTP/2</code> status line over the local HTTP/1.1 server; see the open [response protocol issue](../issues/http-response-uses-invalid-http2-status-line.md).

## Evidence and verification boundary

- [ACL factory](../../src/framework/Libs/Factory.php#L30), [base ACL builder](../../src/framework/Security/Acl.php#L61), and [engine](../../src/framework/Security/Engine/AclEngine.php#L46)
- [Current application ACL selection](../../config/acl.php#L1) and [current app token/handler settings](../../config/config.php#L34)
- [API method resolution](../../src/framework/Handlers/ApiHandler.php#L18), [resource auth check](../../src/framework/Api/ResourceController.php#L36), [ACL callables](../../src/framework/Api/ApiController.php#L121), and [callable gate](../../src/framework/FrontController.php#L96)
- [Focused callable/dispatch tests](../../unit-tests/api/ApiAuthorizationDispatchTest.php) and [literal wildcard tests](../../unit-tests/acl/AclWildcardSemanticsTest.php) passed together with [ACL engine tests](../../unit-tests/acl/AclEngineTest.php#L99), [deny tests](../../unit-tests/acl/AclEngineDenyTest.php#L68), and [compiled-permission tests](../../unit-tests/acl/AclCompiledPermissionsTest.php#L66): 52 tests, 112 assertions. The callable tests use isolated test doubles; none is a live DB-backed request. [Role hierarchy tests](../../unit-tests/acl/RoleHierarchyServiceTest.php#L57) were not included in that run.
