---
name: eventbus-hooks
description: Guide for SimpleRest's EventBus (Observer pattern) and model lifecycle hooks, including when to use each approach.
---

# EventBus & Model Hooks Skill

Two mechanisms to react to events:

| Mechanism | Pattern | Use When |
|-----------|---------|----------|
| Model Hooks | Template Method | Simple, single reaction inside model |
| EventBus | Observer | Multiple listeners, cross-cutting |

## Model Hooks

| Hook | Fires | Must Return |
|------|-------|-------------|
| `boot()` | On instantiation | void |
| `onReading()` | Before SELECT | void |
| `onCreating($data)` | Before INSERT | modified `$data` |
| `onCreated($data)` | After INSERT | void |
| `onUpdating($data)` | Before UPDATE | modified `$data` |
| `onUpdated($data)` | After UPDATE | void |
| `onDeleting($id)` | Before DELETE | void |
| `onDeleted($id)` | After DELETE | void |

### Examples

```php
class ProductModel extends Model {
    protected function onCreating($data) {
        $data['slug'] = Strings::slug($data['name']);
        return $data;  // MUST return $data or null saves!
    }
    protected function onReading() {
        Logger::log('Reading products...');
    }
    protected function onDeleting($id) {
        $product = $this->find($id);
        if ($product['is_protected']) {
            throw new \Exception("Cannot delete protected product");
        }
    }
}
```

## EventBus (Observer Pattern)

### Subject (emits events)

```php
use Boctulus\Simplerest\Libs\EventBusTrait;

class UserModel {
    use EventBusTrait;

    public function save($data) {
        $this->notify('user.before_save', $data);
        // ... save logic ...
        $this->notify('user.created', $data);
    }
}
```

### Observer (listens)

```php
class EmailNotifier implements IObserver {
    public function update($subject, $event, $data = null) {
        if ($event === 'user.created') {
            Mail::send($data['email'], 'Welcome!', '...');
        }
    }
}

class AuditLogger implements IObserver {
    public function update($subject, $event, $data = null) {
        Logger::log("Event: $event", $data);
    }
}
```

### Wiring

```php
$userModel = new UserModel();
$userModel->attach(new EmailNotifier());
$userModel->attach(new AuditLogger());
$userModel->save([...]);   // triggers both observers
```

## When to Use What

### Use Model Hooks When
- Reaction is intrinsic to model (slug, hash password, set UUID)
- Only one thing needs to happen
- Behavior should be always on

### Use EventBus When
- Multiple reactions to same event
- Cross-cutting concerns (logging, email, cache invalidation)
- Decoupled code (observer doesnt know subject)
- Observers should be optional/pluggable

## System Events

| Event | Trigger |
|-------|---------|
| `user.created` | Auth/User model after registration |
| `user.updated` | Model after update |
| `user.deleted` | Model after delete |
| `before.insert` | QB before INSERT |
| `after.insert` | QB after INSERT |

## Pitfalls

1. `onCreating`/`onUpdating` MUST return `$data` or null saves
2. Observers should catch their own exceptions
3. Avoid circular events (observer triggers same event)
4. Detach observers between test cases
5. Hook names are case-sensitive
