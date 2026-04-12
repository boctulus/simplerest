# TODO Must-Do — SimpleRest Roadmap

- Mover el array 'providers' definido dentro config/config.php a un archivo config/providers.php con la estructura:

    return [
        'service_providers' => [
            Boctulus\DummyApi\ServiceProvider::class,
		    Boctulus\FineGrainedACL\FineGrainedAclServiceProvider::class,
            // etc
        ],
        // otro tipo de providers
    ]

- Implementar un ModuleProvider similar al Service Providers de packages. Esto implica registrar los "module providers" en `config/providers.php` a fin de que se ejecuten los metodos boot() y register() del ciclo de vida de los providers.

- Implementar componentes de UI como en "friendlypos" de NodeJs

- Crear sistema de comandos similar al de "friendlypos" (con la misma flexibilidad) en un fork del framework como "Adoon" y luego migrar los comandos viejos al nuevo sistema.


https://chatgpt.com/c/68f84712-fa80-8324-913e-b879767518c1

## 1. Core Stability & Testing
- ✅ Maintain simplicity: arrays over objects.
- [ ] Implement benchmark tests for each core component (Router, QueryBuilder, ORM-lite, Caching, etc.).
- [ ] Automate performance testing to run after every commit.
- [ ] Compare benchmark results over time to prevent regressions.
- [ ] Keep Reflection usage minimal and audited.

## 2. Performance Benchmarking
- [ ] Build a small CLI tool to measure boot time, request handling time, and DB query latency.
- [ ] Benchmark against Laravel and WordPress using equivalent endpoints.
- [ ] Add caching at strategic points (QueryBuilder, autoloading, endpoints).
- [ ] Track and visualize performance deltas in a simple dashboard.

## 3. Documentation & Philosophy
- ✅ Publish `SimpleRest-philosophy.md` publicly.
- [ ] Add minimal yet structured documentation (like Laravel’s, but lighter).
- [ ] Highlight your philosophy: “performance through simplicity.”
- [ ] Explain how the lack of DI containers, PSR overhead, and Reflection magic contributes to speed.
- [ ] Document the automatic endpoint generator and autojoin system as a killer feature.

## 4. Ecosystem & Community
- [ ] Create a clean, minimal website (landing + docs).
- [ ] Offer a GitHub repo with examples and benchmarks.
- [ ] Open issues section for performance discussions and benchmarks from users.
- [ ] Add an optional CLI installer (non-Composer). +

## 5. Branding & Differentiation
- ✅ Keep the name *SimpleRest* — conveys philosophy directly.
- [ ] Use the slogan: “The fastest PHP framework that speaks your logic.”
- [ ] Position it as an “ultra-light alternative to Laravel.”
- [ ] Publish comparative boot-time metrics (Laravel vs. SimpleRest).
- [ ] Focus marketing on measurable data, not buzzwords.

## 6. Strategic Compatibility (Laravel & Composer)
- [ ] Maintain partial Laravel API compatibility (DB facade, helpers, config system). +
- [ ] Create an optional Composer package for users who prefer it, but don’t depend on Composer internally.
- [ ] Ensure it boots in <10ms even when installed via Composer.
- [ ] Offer a Laravel compatibility layer to ease migration of apps. +

---

**Ultimate Goal:**  
Deliver a framework that performs at C-level speed with PHP simplicity, proving that *clarity beats abstraction.*
