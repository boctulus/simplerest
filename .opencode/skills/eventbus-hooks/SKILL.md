---
name: eventbus-hooks
description: Guide for SimpleRest's EventBus (Observer pattern) and model lifecycle hooks, including when to use each approach.
---

# EventBus & Model Hooks Skill

## Two Mechanisms

SimpleRest provides **two** ways to react to application events:

| Mechanism | Pattern | Coupling | Use Case |
|-----------|---------|----------|----------|
| **Model Hooks** | Template Method | Tight (in model) | Simple reactions inside the model |
| **EventBus** | Observer | Loose (decoupled) | Cross-cutting concerns, multiple listeners |

## Model Hooks (Template Method)

Available hooks in any Model subclass:

| Hook | Fires | Return |
|------|-------|--------|
| `boot()` | On model instantiation | void |
| `onReading()` | Before SELECT | void |
| `onCreating($data)` | Before INSERT | modified `$data` |
| `onCreated($data)` | After INSERT | void |
| `onUpdating($data)` | Before UPDATE | modified `$data` |
| `onUpdated($data)` | After UPDATE | void |
| `onDeleting($id)` | Before DELETE | void |
| `onDeleted($id)` | After DELETE | void |

### Examples

```php
class ProductModel extends Model
{
    // Auto-generate slug before creation
    protected function onCreating($data)
    {
        $data['slug'] = Strings::slug($data['name']);
        return $data;   // MUST return $data
    }

    // Log all reads
    protected function onReading()
    {
        Logger::log('Reading products...');
    }

    // Prevent deletion of protected products
    protected function onDeleting($id)
    {
        $product = $this->find($id);
        if ($product['is_protected']) {
            throw new \Exception("Cannot delete protected product");
        }
    }
}
```

> [!IMPORTANT]
> `onCreating()` and `onUpdating()` **MUST return** `$data`. If you return nothing, null will be saved.

## EventBus (Observer Pattern)

**File**: `src/framework/Libs/EventBus.php`  
**Interfaces**: `ISubject`, `IObserver`

### Creating a Subject (emits events)

```php
use Boctulus\Simplerest\Libs\EventBusTrait;

class UserModel {
    use EventBusTrait;

    public function save($data)
    {
        $this->notify('user.before_save', $data);
        // ... save logic ...
        $this->notify('user.created', $data);
    }
}
```

### Creating an Observer (listens to events)

```php
class EmailNotifier implements IObserver
{
    public function update($subject, $event, $data = null)
    {
        if ($event === 'user.created') {
            Mail::send($data['email'], 'Welcome!', '...');
        }
    }
}

class AuditLogger implements IObserver
{
    public function update($subject, $event, $data = null)
    {
        Logger::log("Event: $event", $data);
    }
}
```

### Wiring Together

```php
$userModel = new UserModel();

// Attach multiple observers
$userModel->attach(new EmailNotifier());
$userModel->attach(new AuditLogger());

// Now save triggers both observers
$userModel->save([...]);
// → EmailNotifier sends email
// → AuditLogger logs the creation
```

## When to Use What

### Use Model Hooks When:
- The reaction is **intrinsic** to the model (e.g., setting a slug)
- Only **one** thing needs to happen
- The behavior should be **always on** for that model
- Example: `onCreating` to hash password, `onCreating` to set UUID

### Use EventBus When:
- **Multiple** things need to react to the same event
- The reaction is **cross-cutting** (logging, email, cache invalidation)
- You want **decoupled** code (observer doesn't know about subject)
- Observers should be **optional/pluggable**
- Example: email notification + audit log + cache clear on user creation

## System Events Reference

Built-in events triggered by the framework:

| Event | Triggered By |
|-------|-------------|
| `user.created` | Auth/User model after registration |
| `user.updated` | Model after update |
| `user.deleted` | Model after delete |
| `before.insert` | Query Builder before INSERT |
| `after.insert` | Query Builder after INSERT |

## Best Practices

1. **Hook naming**: Use `{entity}.{action}` (e.g., `order.placed`, `payment.received`)
2. **Observer naming**: Descriptive classes (`EmailNotifier`, `CacheInvalidator`, `AuditLogger`)
3. **Don't mix concerns**: If a hook needs 3+ reactions, switch to EventBus
4. **Error handling**: Observers should catch their own exceptions — don't crash the subject
5. **Detach in tests**: Clean up observers between test cases
6. **Avoid circular events**: An observer that triggers the same event = infinite loop

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Hook not firing | Verify method signature matches exactly (case-sensitive) |
| `onCreating` saves null | Hook MUST `return $data` |
| Observer not called | Ensure `attach()` was called before the event fires |
| Event fires multiple times | Check for duplicate `attach()` calls |
| Exception in observer kills request | Wrap observer logic in try/catch |
