# ACL and API authorization

This page covers the core ACL mechanism and its use by built-in resource API controllers. The active rules and concrete ACL implementation are application-controlled. See the [claim ledger](../audit/authentication-acl.md) for rejected, application-specific, and unresolved legacy claims.

## Rule source and runtime selection

<code>Factory::acl()</code> includes <code>config/acl.php</code>; it does not select a concrete ACL implementation on its own. The file must return an implementation of the core ACL contract. The base <code>Core\Security\Acl</code> is abstract.

The core builder supports named roles, role IDs, a configured guest and registered role, inherited permissions, resource permissions, special permissions, and explicit role denies. <code>addInherit()</code> records a parent and copies the parent's permission arrays at declaration time; the role hierarchy service also uses the recorded lineage for role comparisons.

Permission sources do not form one universal <code>DENY &gt; USER_GRANT &gt; ROLE_GRANT</code> ordering. The compiled path combines role grants, adds per-user special permissions, replaces the role-derived table grant for a resource when a per-user table-permission bitmask is supplied, and then applies explicit denies. Explicit deny wins for the matching permission. A per-user special grant is additive to role special grants. This describes source compilation, not a runtime policy probe; see the ledger for the older command guide's broader claim.

Resource permissions are table/resource actions: <code>show</code>, <code>show_all</code>, <code>list</code>, <code>list_all</code>, <code>create</code>, <code>update</code>, and <code>delete</code>. The builder expands <code>read</code> to <code>show</code> plus <code>list</code>, and <code>write</code> to <code>create</code>, <code>update</code>, and <code>delete</code>. Special permissions are named capabilities such as <code>read_all</code>, <code>write_all</code>, <code>grant</code>, <code>impersonate</code>, <code>fill_all</code>, and <code>transfer</code>. The valid special-permission list can be supplied by an ACL subclass.

This repository's <code>config/acl.php</code> selects the optional <code>FineGrainedACL</code> package, reads role and special-permission names from the database, declares application roles, and writes a serialized ACL file. Its local <code>$acl_cache</code> value is false, so that configuration rebuilds and writes the object rather than restoring it from the file. The <code>BasicACL</code> and <code>FineGrainedACL</code> package directories are optional integrations; the core factory does not auto-discover or bind them.

The core auth controller resolves roles from a user role field when present, otherwise from <code>user_roles</code> and <code>roles</code>. Login and API-key validation load <code>user_tb_permissions</code> and <code>user_sp_permissions</code>; JWT requests carry those permissions in the access-token payload. The <code>FineGrainedACL</code> subclass additionally loads explicit user-deny rows. Database contents and effective runtime policy were not inspected in this phase.

## Automatic resource API authorization

The built-in <code>ApiController</code> path performs these steps:

1. <code>ResourceController</code> calls <code>auth()-&gt;check()</code> during controller construction.
2. <code>ApiController</code> resolves the resource table and asks the ACL for permissions associated with the current identity.
3. It adds allowed action names to the controller callable list.
4. <code>FrontController</code> constructs the controller and then checks that the resolved API method is in that list. A failed check sends 403 before invoking the action method.

The gate runs after controller construction, so the controller constructor itself has run. The check applies to versioned non-auth API dispatch. Auth actions skip this callable gate, and ordinary web controllers have no global ACL guard. A custom API controller that does not use the built-in <code>ResourceController</code>/<code>ApiController</code> path must provide its own credential and authorization behavior; the FrontController callable check only compares method names.

For the ordinary permission path, GET is enabled by <code>read_all</code> or the resource's read actions, POST by <code>write_all</code> or <code>create</code>, PUT/PATCH by <code>write_all</code> or <code>update</code>, and DELETE by <code>write_all</code> or <code>delete</code>. The controller uses one <code>get</code> callable for list and item retrieval.

Missing credentials become the guest role, not an immediate 401. If that role has no matching action, the callable gate responds with 403. Invalid credentials are rejected earlier by the auth check with 401. The current application config declares <code>read_all</code> and <code>write_all</code> for <code>guest</code>; this is application-specific and the optional ACL integration validates special permissions against database rows. No runtime request confirmed its effective result.

## Wildcards, fallback, and API versions

The engine denies an unlisted permission by default. Unknown roles do not contribute allows. <code>read_all</code> and <code>write_all</code> are explicit global capabilities; the compiler expands them to read or write actions across resources. This is not an implicit allow for every role.

The compiled generic <code>can()</code> path also checks a <code>*</code> resource bucket, while the compiled <code>hasResourcePermission()</code> path checks the named resource and wildcard denies but not wildcard allows. The automatic API controller uses <code>hasResourcePermission()</code> and the named special-permission checks. The source therefore does not establish one consistent contract for a literal <code>*</code> resource rule; no test for that literal rule was found. Treat its intended use as unresolved.

<code>ApiHandler</code> validates and retains the route version, but resolves the resource controller from the resource slug. <code>ApiController</code> keys resource permissions by table name and has no separate version key. Application controller code may still branch on the API version; that does not make the ACL version-specific.

## Unresolved authorization discrepancies

- When the requested table has a non-null per-user table-permission bitmask, the <code>ApiController</code> branch adds <code>get</code> for POST and <code>putch</code> for PATCH. <code>ApiHandler</code> resolves those verbs as <code>post</code> and <code>patch</code>, and <code>FrontController</code> compares the strings exactly. Where a role has not already added the matching callable, this branch does not match the dispatched method. The spellings date back to the original 2020 implementation, but no rationale, test, or caller establishing either spelling as intentional was found. Do not rely on this branch without a focused verification.
- <code>Request::method()</code> honors configured URL/header method overrides, and <code>ApiHandler</code> dispatches using that method. <code>ApiController</code> chooses its ACL branch from raw <code>$_SERVER['REQUEST_METHOD']</code>. The two values can differ when an override is used; no focused test or runtime probe established the resulting authorization behavior.

These are source discrepancies with unresolved intent, recorded for later framework investigation. This page does not classify them as defects or change them.

## Record ownership and HTTP status boundary

<code>ApiController</code> has a default owner-scope path for <code>belongs_to</code> resources, and folder access is handled by separate folder-aware code. Those mechanisms do not establish generic row-level or attribute-level ACL rules. The legacy claims about generic row- and field-level ACL are rejected as current framework contracts.

The FrontController passes 403 to its error response when a resolved API action is not callable. The ACL engine itself returns permission results; actual HTTP payload formatting and runtime integration were not probed.

## Evidence and verification boundary

- [ACL factory](../../src/framework/Libs/Factory.php#L30), [base ACL builder](../../src/framework/Security/Acl.php#L61), and [engine](../../src/framework/Security/Engine/AclEngine.php#L46)
- [Current application ACL selection](../../config/acl.php#L1) and [current app token/handler settings](../../config/config.php#L34)
- [API method resolution](../../src/framework/Handlers/ApiHandler.php#L18), [resource auth check](../../src/framework/Api/ResourceController.php#L36), [ACL callables](../../src/framework/Api/ApiController.php#L121), and [callable gate](../../src/framework/FrontController.php#L96)
- Existing [ACL engine tests](../../unit-tests/acl/AclEngineTest.php#L99), [deny tests](../../unit-tests/acl/AclEngineDenyTest.php#L68), [compiled-permission tests](../../unit-tests/acl/AclCompiledPermissionsTest.php#L66), and [role hierarchy tests](../../unit-tests/acl/RoleHierarchyServiceTest.php#L57) cover engine behavior. They do not establish the FrontController/action integration and were not run in this audit.
