---
title: "P0: Secure nested subresource writes in ApiController"
current_step: 0
next_step: 1
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: true
next_step_complexity: high
tags: [security, p0, api, subresources, acl, transactions, microcore]
---

# P0 — Secure nested subresource writes

**Status:** PENDING — task specification only; no implementation or verification performed by this task.

## Problem and evidence

`ApiController` can persist nested resources declared through `$connect_to` as a side effect of writing the parent resource. The parent route's authorization does not, by itself, establish permission to write the related resource or pivot table. Related-controller domain hooks are not necessarily executed by the nested path.

In the GoProp consumer, this design contributed to a **locally demonstrated** privilege escalation through nested `user_roles` on `POST /users` (remediated at the GoProp controller level in commit `7cfb9ceb`, according to its local audit). Another potential bypass is `Properties -> property_co_owners`: the nested write can skip `PropertyCoOwners::resolveCoOwnerLink()` and its organization/ownership checks. This second path is **code-supported but not yet reproduced via authenticated HTTP**. Do not claim it is exploited without an isolated proof.

Review the current canonical `src/framework/Api/ApiController.php` on `master`; it already references `DBRels::getFKs()` in the observed nested paths. Do not reintroduce the obsolete `get_fks()` call or treat that rename as a security fix.

Related consumer investigation:
- [GoProp nested-write finding](https://github.com/boctulus/goprop/blob/main/docs/issues/simplerest-nested-subresource-writes-bypass-acl.md)
- [GoProp P0 identity authorization audit](https://github.com/boctulus/goprop/blob/main/docs/to-do/p0-identity-authorization-hardening.md)

## Non-negotiable architecture constraints

1. Work in the canonical SimpleRest repository (`D:\laragon\www\simplerest`, branch `master`), **never in GoProp's `vendor/` tree** or a PACK/release worktree.
2. Respect the microcore contract in `CLAUDE.md` and `docs/architecture/pack-contract.md`: the distributable kernel must not acquire mandatory database, tenant, or ACL dependencies. Audit the ownership and packaging of `ApiController` and related persistence features **before** choosing the implementation layer. If the current code contradicts the microcore boundary, document the discrepancy and propose the smallest compatible correction; do not silently relocate or rewrite the framework.
3. SimpleRest enforces a generic authorization/validation contract. Consumer-specific rules such as GoProp organization, person identity, property ownership, and percentage constraints must remain in GoProp, exposed to nested writes through an explicit integration point if needed.
4. No implicit trust: merely declaring a relation with `$connect_to` must **not** authorize nested mutation. Default-deny unsafe/undeclared write paths; preserve compatible, explicitly authorized paths.
5. Do not auto-invoke an unrelated controller's hooks as a shortcut without studying side effects and lifecycle compatibility.
6. No publishing, tagging, PACK distribution, deployments, or GoProp edits as part of this task without separate authorization.

## Implementation plan

1. **Reproduce and inventory.** Trace nested POST/PUT/PATCH paths, including 1:1, 1:N, N:M/pivots and payload-key handling. Review `$connect_to`, ACL dispatch, parent/child hooks, schema/FK resolution, and error responses. Use disposable isolated fixtures; never run write probes on shared/production data.
2. **Write failing security tests first.** Demonstrate that permission to create/update a parent does not imply permission to create/update a child or pivot, that nested writes cannot bypass required domain validation, and that unsupported nested payloads fail safely. Distinguish a verified exploit from a suspected path.
3. **Define the authorization contract.** Require explicit permission for each nested mutation (relation + operation + allowed fields), and a framework-supported extension point for application-specific checks before persistence. Do not assume the related resource has a routable controller. Ensure nested metadata and sensitive fields cannot be mass-assigned.
4. **Fix ordering and atomicity.** Validate/authorize parent and nested mutations before committing them; wrap a multi-record mutation in one appropriate DB transaction and roll back **all** changes on failure, including pivot rows. Review current parent/child hook order (especially PUT/PATCH writes performed before `onPuttingAfterCheck()`).
5. **Constrain failure behavior.** Reject denied writes with safe, consistent HTTP errors; remove unsafe debug output that leaks raw SQL/internal details on this path; preserve the original exception in server-side diagnostics without exposing secrets.
6. **Regression and compatibility.** Test allowed nested writes, denied writes, rollback, mixed valid/invalid children, nested metadata, unsupported relationships, read-only/GET behavior, hook execution, and query/schema compatibility. Exercise an isolated GoProp consumer regression for `Users`, `Properties`, and `PropertyCoOwners` without modifying GoProp as part of this task.
7. **Architecture and release gates.** Verify microcore dependency boundaries and isolated microcore tests, the relevant PHPUnit/API suite, CLI and PACK dry-run/build contract where applicable. Capture source SHA and the exact tests executed. Align canonical documentation with *actual* implemented behavior using localized edits and the documentation-governance guard; refresh the inventory if documentation paths/links change. Propose consumer upgrade and rollout steps separately; do not automatically publish.

## Acceptance criteria

- [ ] An unauthorized nested mutation cannot succeed merely because its parent endpoint is writable.
- [ ] Relation/operation/field authorization is enforced server-side for nested children and N:M pivot writes, without importing an ACL or DB dependency into the public microcore.
- [ ] Required application domain policies can run **before** nested persistence; if absent, nested write authorization fails closed rather than silently bypassing checks.
- [ ] POST, PUT and PATCH follow a documented and tested validation/hook order.
- [ ] All writes in one nested operation are atomic; denied/invalid children leave neither parent nor pivot changes committed.
- [ ] Attempts to inject unrelated IDs, organization references, privileged roles, or audit metadata are rejected unless explicitly authorized by the applicable policy.
- [ ] Errors do not disclose SQL statements, secrets, stack traces, or internal diagnostics to API callers.
- [ ] GET/read and explicitly authorized existing nested-write behaviors remain compatible or have a documented, intentionally reviewed migration.
- [ ] Isolated security and regression tests pass; GoProp's reported bypass cases are reproduced or explicitly classified as unproven; no writes are run against shared or production databases.
- [ ] No unapproved modifications to GoProp, `vendor/`, PACK distribution, public tags, or releases.
- [ ] Work is traceable to a scoped commit and a documented source/consumer verification report.

## Out of scope

- Redesign of GoProp roles, employee invitations, co-owner onboarding, or person/account matching.
- GoProp data repair, database constraints, tenant-specific business logic, or production deployment.
- Broad core/module refactoring unrelated to securing nested writes.

**Important:** Keep this issue open until the framework-level problem and its consumer regression have been verified. An application-specific controller guard alone is a mitigation, not completion of this task.
