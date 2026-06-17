# EventBus — Sistema de Eventos

## Descripción

**EventBus** implementa el patrón **Observer** (o Event Bus). Permite que componentes del sistema reaccionen a eventos sin acoplamiento directo.

**Archivo**: `src/framework/Libs/EventBus.php`  
**Interfaces**: `ISubject`, `IObserver`

---

## Uso

### Sujeto (emisor)

```php
use Boctulus\Simplerest\Libs\EventBus;

// Usando el trait EventBusTrait
class UserModel {
    use EventBusTrait;

    public function save($data)
    {
        // ... lógica de guardado ...
        $this->notify('user.created', $data);
    }
}
```

### Observer (receptor)

```php
class EmailNotifier implements IObserver
{
    public function update($subject, $event, $data = null)
    {
        if ($event === 'user.created') {
            // Enviar email de bienvenida
        }
    }
}

// Registrar observer
$userModel = new UserModel();
$userModel->attach(new EmailNotifier());
```

## Eventos del Sistema

| Evento | Disparado por |
|--------|---------------|
| `user.created` | Model al crear usuario |
| `user.updated` | Model al actualizar |
| `user.deleted` | Model al eliminar |
| `before.insert` | Query Builder antes de insert |
| `after.insert` | Query Builder después de insert |

## Model Hooks (Template Method)

El modelo base también expone hooks directos (alternativa a EventBus):

| Hook | Momento |
|------|---------|
| `boot()` | Al instanciar el modelo |
| `onReading()` | Antes de leer datos |
| `onCreating($data)` | Antes de crear |
| `onCreated($data)` | Después de crear |
| `onUpdating($data)` | Antes de actualizar |
| `onUpdated($data)` | Después de actualizar |
| `onDeleting($id)` | Antes de eliminar |
| `onDeleted($id)` | Después de eliminar |

```php
class ProductModel extends Model
{
    protected function onCreating($data)
    {
        $data['slug'] = Strings::slug($data['name']);
        return $data;
    }
}
```

## Ver También

- [`Framework-Architecture.md`](./Framework-Architecture.md)
