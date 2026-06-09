# SimpleRest — Philosophy and Performance Principles

## 1. Introduction

**SimpleRest** is a high-performance PHP framework built from scratch to achieve extreme speed, simplicity, and predictability.

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

## 6. PHP Compatibility (8.1+)

* Fully compatible with PHP **8.1 through 8.4**.

| PHP Compatibility    | 8.1–8.4                   | 8.1+                        | 8.1+               |

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
