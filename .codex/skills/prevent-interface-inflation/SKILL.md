---
name: prevent-interface-inflation
description: Prevent premature abstractions and unnecessary interfaces.
---

# SKILL: Prevent Interface Inflation

# Purpose

Prevent:

```txt
premature decomposition
interface inflation
accidental enterprise architecture
hypothetical abstractions
micro-service mentality inside monoliths
```

# Core Rule

Every abstraction must eliminate measurable complexity.

If the abstraction does not clearly reduce:

* coupling
* volatility
* infrastructure dependency
* runtime polymorphism
* orchestration complexity

then it must NOT exist.

---

# Interfaces Are Allowed Only When

* multiple real implementations exist
* a stable architectural boundary exists
* infrastructure isolation is required
* runtime polymorphism is operationally necessary

Otherwise:

```txt
do not create the interface
```

---

# Forbidden Patterns

Avoid:

```txt
FooInterface + Foo
```

when only one implementation exists.

Avoid:

```txt
Service
Manager
Resolver
Provider
Coordinator
Handler
```

unless the name represents a real operational responsibility.

Avoid:

```txt
micro-abstractions
pass-through services
semantic wrappers
hypothetical extensibility
```

---

# Extraction Rule

Before extracting a class ask:

```txt
What concrete complexity disappears if this abstraction exists?
```

If the answer is unclear:

```txt
keep the logic cohesive and local
```

---

# Architectural Bias

Prefer:

```txt
high cohesion
few boundaries
explicit behavior
large cohesive modules
```

Over:

```txt
many files
many interfaces
many layers
premature decomposition
```

---

# Enterprise Rule

Enterprise-grade means:

```txt
clear boundaries
deterministic behavior
predictable evolution
```

NOT:

```txt
more abstractions
```
