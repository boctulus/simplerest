<?php

namespace Boctulus\Simplerest\Core\Interfaces;

/**
 * Composite ACL contract.
 *
 * Extends the three domain-specific interfaces:
 *   IAclBuilder  — build-time policy construction
 *   IAclPolicy   — pure reads over the constructed policy
 *   IAclRuntime  — runtime evaluation (auth-aware)
 *
 * Existing code that type-hints IAcl continues to work unchanged.
 * New code should prefer the narrower interface that matches its actual need.
 */
interface IAcl extends IAclBuilder, IAclPolicy, IAclRuntime { }
