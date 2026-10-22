# SimpleRest Integrations

Optional integrations and UI helpers for SimpleRest. This package contains Google Drive access, WooCommerce clients, OneSignal and Hugging Face clients, CMS detection, Symfony-based HTML scraping, and a Bootstrap paginator.

The package does not contain copied Google, Symfony, JavaScript, or CSS distributions. Composer installs the declared PHP dependencies in the consuming project. Google Drive is isolated further: install `google/apiclient` only when using `GoogleDrive`; the client reports a clear error if its SDK is absent. The `boctulus/simplerest` core remains independent of this package and its dependencies.

The implementation namespace is `Boctulus\Simplerest\Integrations\Libs`. In the unpublished core candidate, the former `Boctulus\Simplerest\Core\Libs` class names remain as compatibility aliases when this package is installed. A consumer using one of those features must require this package explicitly. The published core `v2.0.2` still contains its original implementations.

This package is not registered on Packagist. Its manifest and tests are prepared for an independent Composer VCS install, but publication needs a reviewed repository split and API migration review.
