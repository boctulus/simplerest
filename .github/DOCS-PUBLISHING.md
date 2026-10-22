# Publishing the SimpleRest documentation

The official public documentation source is the VitePress site in `docs-site/`. It is a **curated Spanish derivative** of the canonical material listed in `docs/canonical-manifest.json`. Do not publish `docs/` directly: it contains internal audits, legacy material, and unfinished documentation.

## Published public URL

`https://boctulus.github.io/simplerest/`

The site is published from `master`. PR #2 merged as commit `0c7ef58`, and publication run `39725153608` completed both the build and deployment jobs. The repository README now links to this URL. VitePress has `base: '/simplerest/'` for this repository Pages URL. Recheck the deployment and its assets if the Pages source, repository name, or base path changes. A custom-domain migration requires changing that base, the Pages setting, and testing internal links and assets.

## First-time setup

1. On GitHub, open **Settings → Pages → Build and deployment**, and select **GitHub Actions** as the source.
2. Merge the publication changes into the repository's default branch, `master`, after the pull-request build succeeds.
3. Open **Actions → Public documentation** and verify that both `Build public docs` and `Deploy public docs` succeed. The workflow also supports **Run workflow** on `master`.
4. Visit the URL above and confirm that CSS, JavaScript, local search, navigation, and direct deep links work from the `/simplerest/` prefix. Verify desktop and mobile layouts.
5. After verifying the deployed URL, link to it from the repository README. Check the same URL again before adding it to Packagist, the framework website, or release materials.

The workflow runs `npm ci` and `npm run build` from `docs-site/`. Pull requests only build and check the site; pushes to `master` build and deploy. The Pages environment needs the repository's GitHub Pages feature enabled. Neither an artifact upload nor a green pull-request build proves that the public URL is live.

## Local validation

```sh
cd docs-site
npm ci
npm run build
npm run preview
```

Inspect internal links as well as the final site output. Do not alter the root `package.json` or use an unpinned installation in CI.

## Editorial release gate

The first publication includes eight curated pages: the landing page, Getting Started, Architecture, Request Lifecycle, Database, HTTP/API, Security and Webhooks. Pages carry canonical `source`, `source_revision`, and `status` metadata. Their `partial` status must remain explicit until supported by a fresh audit.

For every behavioral change:

1. Inspect implementation, configuration and relevant tests; record evidence and any unresolved claims in `docs/audit/`.
2. Apply only evidence-backed, localized edits to the corresponding canonical file in `docs/`. Follow `docs/documentation-governance.md`; never regenerate canonical documents for stylistic reasons.
3. Update the derived Spanish page, refresh `source_revision` to the audited source commit, retain explicit limitations and keep internal-only links out of the public page.
4. Run `php scripts/documentation-governance-guard.php` and the VitePress build, then review the rendered navigation and links in the pull request.

Do not market the initial publication as complete installation, portable Composer integration, database CRUD coverage, tested CLI workflows, or production deployment guidance. The outstanding work is tracked in `docs/to-do/documentation-audit-continuation.md` and `docs/to-do/gaps-tecnicos-documentacion-canonica.md`; these documents are not website pages.

## Subsequent releases

Publish from `master` only. Prefer small reviews grouped by topic rather than promoting unverified legacy Markdown. Translate only after the Spanish derivative is consistent with its canonical sources. If a custom domain is introduced, set `base: '/'` for deployment at the domain root (or the actual subpath), confirm Pages DNS/HTTPS settings, and validate all links before switching public entry points.
