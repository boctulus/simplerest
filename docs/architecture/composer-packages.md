# SimpleRest Composer package boundaries

## Existing package identities

This design reuses the Composer names already present in the repository. It does not add a `simplerest-cli` package or another core package.

| Composer name | Responsibility | Distribution decision |
| --- | --- | --- |
| `boctulus/simplerest` | Runtime framework core, including routing, HTTP, database abstractions, models, schema tools, security primitives, and the CLI contract/registry | Keep the existing root package identity. Its Composer autoload contains only `src/framework/`; Git archive exclusions reduce the installable `dist` artifact. |
| `boctulus/simplerest-code-tools` | Optional PHP parser and reusable `make` command implementations | Keep the existing identity. Split this directory reproducibly from the monorepo for its own Git release history. Applications opt in to its commands. |
| `boctulus/simplerest-pdf` | Optional `PdfBase` and FPDF integration | Keep the existing identity. Split this directory reproducibly from the monorepo for its own Git release history. |
| `boctulus/basic-acl` | Proprietary basic ACL extension | Keep as an existing local package. Its proprietary license means this work does not publish it publicly. |
| `boctulus/fine-grained-acl` | Proprietary fine-grained ACL extension | Keep as an existing local package. GoProp retains its application-owned copy until a private distribution is independently authorized and validated. |

The root SimpleRest checkout remains the development application and source monorepo. Production installation uses only the framework PSR-4 mapping. Development application mappings and local package mappings live under `autoload-dev`.

## CLI ownership and registration

`BaseCommand`, `CommandRegistry`, dispatch, parsing, help, and collision checks remain in the core. GoProp and every host application retain their own `app/Commands/` directory and `com` entry point.

`boctulus/simplerest-code-tools` contains the reusable `make` group and PHP source tools. The host application must list the package in `config/commands.php` to expose its commands; installation alone does not enable them. Composer-installed commands are loaded through the package classmap, and their classes use the package namespace so they do not collide with host classes that share a short name.

`CommandRegistry` resolves providers in this order: host application, explicitly enabled extensions, configured framework defaults. Cross-tier command or alias collisions choose the higher tier and emit a diagnostic. Same-tier collisions fail unless `overrides` identifies one provider. Overrides can select a lower tier explicitly. Host commands use provider ID `application`.

## Dependency contracts

The core manifest is the source of truth for its PHP and runtime dependency constraints. GoProp's scraper uses the framework `DomCrawler` wrapper, so its base class is part of the core dependency contract. PHPMailer, Google API Client, and Guzzle are optional integrations and are not core installation requirements. `Mail::send`, Google Drive, and SendinBlue report a clear runtime error when their selected adapter lacks its required library; SendinBlue also requires its SDK classes to be supplied by the host application.

The PDF package owns FPDF. The code-tools package owns PHP Parser. Neither dependency is installed by a production core-only consumer.

The root development checkout keeps Dotenv and test/tool dependencies under `require-dev`. The curated starter pack has its own runtime manifest and retains Dotenv because its application bootstrap loads `.env` files.

## Compatibility boundary

Version 2 of the core removes `Core\Libs\PdfBase` and `Core\Libs\PHPParser` from the core tree. Their existing namespaces are provided by the existing PDF and code-tools package names. This is a major-version change and must not be backported to `v1.0.3` or represented by moving an existing tag.

GoProp currently declares PHP `>=7.4,<8.4`; the SimpleRest core declares `>=8.1,<8.5`. Their supported intersection is PHP 8.1 through 8.3. The GoProp `dev` migration must state this narrowed application support range explicitly and test it before the branch is offered for integration.

## Distribution limits

The root package's GitHub/Composer `dist` archive excludes the development application, tests, docs, local packages, assets, and tooling through `.gitattributes`. This reduces normal Composer `dist` installs. It does not reduce `--prefer-source`: that mode still clones the SimpleRest development repository and its history. The release record reports both facts rather than treating archive exclusions as source-repository modularization.

The local package directories and Composer identities exist, but they are not all public distributions. As of 2026-10-22, Packagist returns 404 for the existing `simplerest-code-tools`, `simplerest-pdf`, `basic-acl`, and `fine-grained-acl` names; the public split repositories for Code Tools and PDF are not available. Reuse these names and directories. Do not add a duplicate package identity. Public Composer installation remains gated on publishing the existing optional packages and registering their repositories.

Path-repository consumer tests are an iteration check only. Release readiness requires a clean install from the committed VCS/archive references, with the resolved commit recorded.

The two optional MIT packages use `git subtree split` from an explicit immutable SimpleRest commit. The script maps only their existing Composer names to the corresponding package directories and repositories. Proprietary ACL packages are outside that public split pipeline.
