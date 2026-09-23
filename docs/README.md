# SimpleRest documentation

This directory is the maintained documentation home for SimpleRest. Pages are organized by reader task and topic. The source code and tests define the implementation; a statement is not verified merely because it appears in an older guide.

## Start here

- [Documentation status and evidence rules](audit/README.md)
- [Architecture overview](architecture.md)
- [Getting started](getting-started/README.md)

## Topic guides

These sections will be populated as each topic is traced to implementation and, where available, tests and runnable examples.

- [Core](core/README.md)
- [Database](database/README.md)
- [HTTP and REST API](api/README.md)
- [Command line](cli/README.md)
- [Integrations](integrations/README.md)
- [Deployment](deployment/README.md)
- [Reference](reference/README.md)

## Historical material

The original monolithic document is retained at [`framework/_archive/DOC-Simplerest.txt`](framework/_archive/DOC-Simplerest.txt) as historical material. It is not a current specification. Existing pages under `framework/` are being reviewed; until a page is explicitly audited, treat it as an unverified source to compare with code.

## Documentation policy

Each concept should have one owning page. Tutorials may link to that page rather than restating its full reference. Pages should describe SimpleRest's own interfaces and runtime, distinguish implemented behavior from plans, and avoid importing architecture from another framework by analogy. See the [audit register](audit/README.md) for current findings and scope.
