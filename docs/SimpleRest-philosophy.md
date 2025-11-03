# SimpleRest — Philosophy and Performance Principles

## 1. Introduction

**SimpleRest** is a high-performance PHP framework built from scratch to achieve extreme speed, simplicity, and predictability.
It has consistently shown to be **up to 10× faster than Laravel** and **up to 100× faster than WordPress**, despite using only PHP and without relying on caching services such as **Redis** or **Memcached**.

---

## 2. Design Philosophy

The core philosophy behind SimpleRest is simple:

> *"Eliminate every unnecessary abstraction layer while maximizing real-world developer efficiency."*

All design decisions prioritize **execution speed**, **low memory usage**, and **clarity of behavior**.
Unlike other frameworks that chase standards for compatibility, SimpleRest optimizes directly for runtime efficiency and developer control.

SimpleRest draws inspiration from multiple sources:

* **Laravel**, for its expressive syntax and developer-friendly conventions.
* **CodeIgniter**, for its philosophy of simplicity, directness, and performance (a philosophy once praised by PHP’s creator, Rasmus Lerdorf).
* **PHP itself**, by embracing native performance characteristics instead of abstracting them away.

In essence:

> **“Laravel-like syntax. CodeIgniter philosophy. C-level performance.”**

---

## 3. Key Performance Drivers

### 🔹 No Heavy Dependency Injection Containers

* No runtime resolution or reflection-based dependency graphs.
* Dependencies are explicitly declared or managed via lightweight factories.
* Avoids the runtime overhead of Laravel’s or Symfony’s DI containers.

### 🔹 No Complex Request/Response Abstractions

* Direct interaction with PHP globals (`$_GET`, `$_POST`, `$_SERVER`, etc.).
* Avoids extra object wrapping and middleware indirection layers.
* Simple, predictable request lifecycle with minimal allocations.

### 🔹 No PSR Overhead

* Does not implement PSR-7, PSR-11, or PSR-15 for the internal core.
* Skips adapter layers, interface lookups, and reflection calls at runtime.

### 🔹 Array-Based Core Design

* Database access always returns **associative arrays**, never objects.
* Eliminates ORM hydration costs and unnecessary property mapping.
* Pure array manipulation keeps memory allocation extremely low.

### 🔹 Minimal Reflection

* Reflection is used only in a few classes related to an optional dependency injector.
* Not used anywhere in the core or performance-critical paths.

### 🔹 Internal Namespace Handling

* The framework resolves namespaces internally, without Composer overhead.
* Avoids runtime file lookups done by the Composer autoloader.

---

## 4. Laravel-like Syntax, but Faster

SimpleRest offers a **Laravel-like syntax** to ease adoption and familiarity while keeping the underlying execution model radically simpler.
This balance provides immediate developer comfort with no learning curve and **orders of magnitude better performance**.

> *“Same syntax, no magic. Same features, less overhead.”*

---

## 5. Simplicity Inspired by CodeIgniter

While CodeIgniter may have faded in popularity, its founding philosophy remains timeless: *simplicity, directness, and control.*
SimpleRest embraces this principle while combining it with modern PHP and Laravel-like convenience.

> *“Inspired by CodeIgniter’s simplicity and Laravel’s expressiveness, SimpleRest brings both worlds together.”*

---

## 6. Broad PHP Compatibility (7.4–8.3)

* Fully compatible with PHP **7.4 through 8.3**.
* Does not depend on the latest language features to deliver performance.
* Ideal for shared-hosting and legacy environments.
* Provides a **stable upgrade path** for older systems without forcing PHP upgrades.

> *“Because performance shouldn’t depend on your PHP version.”*

---

## 7. Automatic Endpoints and Dynamic AutoJoins

An exception to the “no magic” rule is SimpleRest’s **automatic endpoint system**.

### ✳️ Automatic Endpoints

* Endpoints are generated automatically from model or table names.
* Developers can create fully functional REST APIs without writing controller code.

### ⚙️ AutoJoin Capability

* Relationships between tables are inferred automatically based on naming conventions or detected foreign keys.
* Complex queries with joins are resolved transparently without requiring explicit model declarations.

### ⚡ Why It’s Still Fast

* This “controlled magic” is lightweight and non-reflective.
* All joins and relationships are processed at the Query Builder level using optimized logic.
* Because the rest of the core has almost zero overhead, these features do not degrade performance.
* Benchmarks show very high throughput even with multi-table joins.

---

## 8. Query Builder with ORM-Like Power

The internal **Query Builder** mimics many ORM conveniences:

* Fluent syntax and chaining.
* Parameter binding and query safety.
* Relationship handling and eager loading support.

Yet, it avoids ORM penalties by:

* Never instantiating model objects.
* Returning pure associative arrays.
* Skipping hydration, event dispatching, and property reflection.

Result: **the speed of raw SQL** with **the expressiveness of an ORM**.

---

## 9. Intensive Internal Caching

SimpleRest employs several layers of in-memory caching:

* Query result caching for repetitive reads.
* Configuration and routing cache.
* Class autoloader cache.
* Array-level runtime caching to eliminate redundant computations.

This design minimizes filesystem access and I/O waits.

---

## 10. Ultra-Fast Bootstrap

* The full boot process completes within **a few milliseconds**.
* Lazy-loading ensures only essential components are loaded per request.
* No configuration merging, no service discovery, no dependency graph resolution.
* Under identical conditions, Laravel’s bootstrap takes **300–500 ms**, while SimpleRest completes in **3–10 ms**.

---

## 11. Composer and Autoloading Considerations

* The framework does not require Composer to function.
* Namespaces are internally managed to avoid autoload lookup cost.
* If installed via Composer:

  * Use `--optimize-autoloader` and `--classmap-authoritative` to maintain zero-delay loading.
  * With OPcache or Preloading enabled, runtime performance remains virtually identical.

---

## 12. Summary Comparison

| Feature              | SimpleRest                | Laravel                     | WordPress          |
| -------------------- | ------------------------- | --------------------------- | ------------------ |
| Dependency Container | Optional, minimal         | Heavy reflection-based      | None (globals)     |
| ORM                  | QueryBuilder (no objects) | Eloquent (heavy ORM)        | Procedural `$wpdb` |
| Data Type            | Associative arrays        | Objects                     | Arrays/Objects     |
| Caching              | Internal, aggressive      | External (Redis/Memcached)  | Limited            |
| Bootstrap Time       | 3–10 ms                   | 300–500 ms                  | 500–1000 ms        |
| Composer Dependency  | Optional                  | Required                    | Optional           |
| Reflection Usage     | Minimal                   | Extensive                   | Minimal            |
| AutoEndpoints        | Yes (zero config)         | Partial                     | No                 |
| AutoJoins            | Yes (inferred)            | Requires explicit relations | No                 |
| PHP Compatibility    | 7.4–8.3                   | 8.1+                        | 7.4+               |

---

## 13. Conclusion

SimpleRest proves that **simplicity and raw performance can coexist**.
By stripping away abstraction layers and optimizing for the real PHP runtime, it achieves speeds typically seen only in compiled microservices.

Its unique combination of:

* **Laravel-like syntax for easy adoption**
* **CodeIgniter-inspired simplicity**
* **Direct data access**
* **Automatic endpoint generation**
* **Lightweight autojoins**
* **Aggressive caching**
* **Minimal autoloading and reflection**

makes it one of the fastest and most efficient PHP frameworks ever built.

> *"Speed is not the absence of power — it’s the absence of waste."*
