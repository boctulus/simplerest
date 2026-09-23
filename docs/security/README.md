# Security

These references describe authentication and authorization behavior traced to the current source. They distinguish framework mechanisms from this repository's application configuration and optional integrations.

- [Authentication](authentication.md) — credential detection, validation, identity representation, and source-level status behavior.
- [ACL and API authorization](acl.md) — ACL rule inputs, resolution, automatic API callables, route versions, and known unresolved behavior.
- [Claim-level audit ledger](../audit/authentication-acl.md) — legacy claims, classifications, test coverage, and verification limits.

The source and configuration trace is complete for this phase. No HTTP runtime probe or end-to-end authentication/authorization run was performed.
