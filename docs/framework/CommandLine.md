# Sistema de Comandos CLI

El framework incluye un sistema de comandos CLI accesible con:

```bash
php com <grupo> <comando> [--opciones]
php com help                        # lista todos los grupos
php com <grupo> help                # lista comandos del grupo
php com <grupo> <comando> --help    # ayuda de un comando específico
```

---

## Sistema de grupos

Los comandos se organizan en **grupos**, cada uno en su propio directorio:

```
app/commands/
├── acl/          → 23 comandos (php com acl ...)
├── doc/          →  1 comando
├── file/         →  1 comando
├── make/         → 42 generadores de código
├── migrations/   → 15 comandos
├── module/       →  1 comando
├── mysql_log/    →  4 comandos
├── pack/         →  1 comando
├── router/       →  1 comando
├── sql/          → 11 comandos
├── system/       →  2 comandos
├── test/         →  4 comandos
├── users/        → 11 comandos
└── _disabled/    (comandos desactivados — legado)
```

Cada archivo dentro de un grupo es un comando independiente. El dispatcher los descubre automáticamente por nombre de clase y directorio.

---

## Crear un comando nuevo

```bash
php com make command myCommand
```

Genera `app/commands/<grupo>/MyCommandCommand.php` con el esqueleto base.

---

## Patrón moderno: BaseCommand

Todos los comandos activos extienden `BaseCommand`:

```php
<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

class CreateUserCommand extends BaseCommand
{
    public string $group = 'users';

    public function __construct()
    {
        $this->command     = 'create-user';
        $this->description = 'Crea un nuevo usuario';
        $this->aliases     = ['create', 'new', 'add'];
        $this->examples    = [
            'php com users create-user --email=user@example.com --password=secret123',
            'php com users create-user --email=admin@example.com --password=secret123 --role=admin',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'password'],
            'optional' => ['username', 'firstname', 'lastname', 'display-name', 'role'],
            'flags'    => ['dry-run', 'verbose'],
            'options'  => [
                'email'    => ['describe' => 'Email del usuario'],
                'password' => ['describe' => 'Contraseña'],
                'role'     => ['describe' => 'Rol a asignar', 'default' => 'registered'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $email   = $this->opt($parsed, 'email');
        $password = $this->opt($parsed, 'password');
        $role    = $this->opt($parsed, 'role');          // usa default si no se pasó
        $dryRun  = $this->opt($parsed, 'dry_run', false);

        if ($dryRun) {
            $this->log("Crearía usuario: {$email}", 'info');
            return;
        }

        // lógica real aquí
        $this->log("Usuario creado: {$email}", 'success');
    }
}
```

### Propiedades del comando

| Propiedad | Tipo | Descripción |
|-----------|------|-------------|
| `$group` | `string` | Nombre del grupo (`acl`, `users`, etc.) |
| `$command` | `string` | Nombre del comando (`create-user`) |
| `$description` | `string` | Descripción para el `help` |
| `$aliases` | `array` | Aliases alternativos (`create`, `new`, `add`) |
| `$examples` | `array` | Líneas de ejemplo para el `help` |

### config() — declaración de argumentos

```php
public static function config(): array
{
    return [
        'required' => ['email', 'password'],   // el dispatcher valida presencia
        'optional' => ['role', 'username'],    // opcionales, no validados
        'flags'    => ['dry-run', 'force'],    // booleanos — default false
        'options'  => [
            'email'  => ['describe' => 'Email del usuario'],
            'role'   => ['describe' => 'Rol', 'default' => 'registered'],
        ],
    ];
}
```

- `required` — el dispatcher llama a `validate()` automáticamente y aborta si falta alguno.
- `flags` — se normalizan a snake_case: `--dry-run` → `$parsed['dry_run']`.
- `options['default']` — se inyecta automáticamente en `$parsed` si el flag no fue pasado.

### execute(array $parsed)

Recibe el array ya parseado y validado. Las claves están en `snake_case`:

```
--email=foo@bar.com   → $parsed['email']
--dry-run             → $parsed['dry_run']  (true/false)
--role=admin          → $parsed['role']
palabra_suelta        → $parsed['_positional'][0]
```

### opt() — leer argumentos con default

```php
$email  = $this->opt($parsed, 'email');               // null si no existe
$limit  = $this->opt($parsed, 'limit', 100);          // 100 si no fue pasado
$dryRun = $this->opt($parsed, 'dry_run', false);
```

Maneja automáticamente la conversión kebab-case → snake_case.

### Argumentos posicionales

Los argumentos sin `--` se almacenan en `$parsed['_positional']`:

```bash
php com acl show-user-roles user@example.com
# → $parsed['_positional'][0] = 'user@example.com'
```

Patrón idiomático para aceptar ambas formas:

```php
$email = $this->opt($parsed, 'email') ?? ($parsed['_positional'][0] ?? null);
```

### Helpers de output

```php
$this->log("Mensaje informativo", 'info');     // ℹ Mensaje
$this->log("Operación exitosa",   'success');  // ✓ Operación exitosa
$this->log("Hubo un error",       'error');    // ✗ Hubo un error
$this->log("Atención",            'warning');  // ⚠ Atención
$this->showUsage();                            // imprime $this->examples
```

---

## Comandos con operaciones destructivas

Para comandos destructivos usar `requireConfirm()` heredado de `BaseAclCommand` o implementar el mismo patrón:

```php
public function execute(array $parsed): void
{
    $dryRun = $this->opt($parsed, 'dry_run', false);

    if ($dryRun) {
        echo "  [dry-run] Se eliminarían N registros de {$table}\n";
        return;
    }

    if (!$this->requireConfirm($parsed)) {
        return;   // requiere --force o --yes para continuar
    }

    // ejecutar la operación destructiva
}
```

Flags aceptados por `requireConfirm()`: `--force`, `--yes`, `--confirm`.

---

## Comandos base reutilizables

Para grupos con lógica compartida se crea una clase base intermedia:

```
app/commands/acl/BaseAclCommand.php   ← helpers de DB, ACL, output en tabla
app/commands/users/BaseUsersCommand.php
```

La clase base extiende `BaseCommand` y los comandos del grupo extienden la clase base. No redefinir `$group` en cada comando — se define en la base o en cada uno según convenga.

---

## Comando SQL

El grupo `sql` permite operar sobre la base de datos desde la terminal.

### sql find

Busca un registro por clave primaria.

```bash
php com sql find '{db}.{table}' --id={value} [--format=table]
```

- `{db}.{table}` — conexión y tabla en formato `conexion.tabla` (ej: `main.users`)
- `--id` — valor de la clave primaria (detectada automáticamente desde el schema)
- `--format=table` — salida en tabla ASCII (por defecto: bloques clave-valor)

```bash
php com sql find 'zippy.products' --id=217548
php com sql find 'main.users'     --id=5 --format=table
```

Requiere schema en `app/Schemas/{db}/{Table}Schema.php`.

---

### sql list

Lista registros con paginación.

```bash
php com sql list '{db}.{table}' [--take=N] [--skip=M] [--format=table]
```

Aliases: `--limit` = `--take`, `--offset` = `--skip`. Default: 10 registros.

```bash
php com sql list 'main.users'
php com sql list 'main.users' --take=20 --format=table
php com sql list 'main.users' --skip=10 --limit=5
```

---

### sql search

Busca registros por texto en campos STR/TEXT del schema.

```bash
php com sql search '{db}.{table}' --search='texto' [--take=N] [--format=table]
```

Usa LIKE con OR sobre todos los campos de texto del schema.

```bash
php com sql search 'zippy.products' --search='MEDALLON'
php com sql search 'main.users'     --search='john' --format=table
```

---

### Formatos de salida

**Formato simple** (por defecto):
```
Record #1:
  ean: 217548
  description: MEDALLON POLLO MB CONG.
```

**Formato tabla** (`--format=table`):
```
+--------+-------------------------+
| ean    | description             |
+--------+-------------------------+
| 217548 | MEDALLON POLLO MB CONG. |
+--------+-------------------------+
```

---

### Notas SQL

- `{db}` es el nombre de la **conexión** en `config/databases.php`, no el nombre de la base de datos.
- `find` y `search` requieren schema; `list` no lo requiere.

---

## Helpers del MakeCommand

Disponibles en los comandos del grupo `make`:

### renderTemplate

Crea un archivo desde una plantilla con sustitución de nombre y namespace.

```php
$this->renderTemplate(
    $name,           // nombre base (ej: 'my_controller')
    $prefix,         // prefijo de clase (ej: 'I' para interfaces)
    $subfix,         // sufijo (ej: 'Controller')
    $dest_path,      // ruta destino
    $template_path,  // ruta de la plantilla
    $namespace,      // namespace PHP
    '--force'        // opciones: --force, --strict, --remove, --unignore
);
```

Ejemplos:

```php
// Crear controlador
$this->renderTemplate('my_controller', '', 'Controller',
    CONTROLLERS_PATH, self::TEMPLATES . 'Controller.php',
    $this->namespace . '\\controllers', '--force');

// Crear interfaz
$this->renderTemplate('my_interface', 'I', '',
    INTERFACE_PATH, self::INTERFACE_TEMPLATE,
    $this->namespace . '\\interfaces', '--strict');

// Eliminar
$this->renderTemplate('my_controller', '', 'Controller',
    CONTROLLERS_PATH, self::TEMPLATES . 'Controller.php',
    $this->namespace . '\\controllers', '--remove');
```

---

### makeScaffolding

Crea una estructura de directorios para scaffoldings.

```php
$this->makeScaffolding($directories, $basePath, $options);
```

```php
// Crear estructura de módulo
$this->makeScaffolding(
    ['assets/css', 'config'],
    MODULES_PATH . 'myModule',
    ['--force']
);

// Eliminar estructura
$this->makeScaffolding(
    ['assets/css', 'config'],
    MODULES_PATH . 'myModule',
    ['--remove']
);
```
