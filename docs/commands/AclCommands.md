# Referencia de Comandos ACL

**Grupo**: `php com acl`  
**Total**: 23 comandos  
**Última actualización**: 2026-05-20

---

## Acceso rápido

```bash
php com acl help                        # lista completa
php com acl <comando> --help            # detalle de un comando
```

---

## Resumen de comandos

| Comando | Descripción breve | Destructivo |
|---------|-------------------|-------------|
| `make` | Genera el ACL en DB desde `config/acl.php` | con `--force` |
| `validate` | Verifica consistencia del ACL | No |
| `list-roles` | Lista todos los roles registrados en DB | No |
| `inspect-role` | Muestra la política compilada de un rol | No |
| `list-user-roles` | Todos los usuarios con sus roles | No |
| `show-user-roles` | Roles de un usuario específico | No |
| `assign-role` | Asigna rol a un usuario | No |
| `remove-role` | Quita un rol de un usuario | No |
| `list-sp` | Lista todos los special permissions disponibles | No |
| `grant-sp` | Otorga un SP a un usuario | No |
| `revoke-sp` | Revoca un SP de un usuario | No |
| `list-user-sp` | SPs individuales de un usuario | No |
| `grant-tb` | Agrega permiso de tabla a un usuario | No |
| `revoke-tb` | Quita permiso de tabla de un usuario | No |
| `list-user-tb` | Permisos de tabla de un usuario | No |
| `clear-tb` | Elimina **todos** los permisos de tabla de un usuario | **Sí** |
| `replace-tb` | Reemplaza el set completo de permisos de tabla | **Sí** |
| `add-deny` | Agrega una deny rule a un usuario | No |
| `remove-deny` | Elimina una deny rule | No |
| `list-deny` | Lista deny rules de un usuario | No |
| `can` | ¿Tiene el usuario ese permiso? (boolean) | No |
| `explain` | Resolution chain completo de un permiso | No |
| `resolve` | Permisos efectivos compilados de un usuario | No |

---

## Referencia detallada

### make

Genera los roles y permisos en DB desde `config/acl.php`.

```bash
php com acl make
php com acl make --force        # elimina roles previos antes de generar
php com acl make --debug        # muestra el ACL generado
```

Aliases: `generate`, `gen`, `build`

---

### validate

Verifica consistencia del ACL: roles huérfanos, permisos inválidos, herencia rota, deny rules inválidas.

```bash
php com acl validate
```

---

### list-roles

Lista todos los roles registrados en DB.

```bash
php com acl list-roles
php com acl list-roles --format=json
```

Alias: `ls-roles`

---

### inspect-role

Muestra la política compilada de un rol (source: `config/acl.php`).

```bash
php com acl inspect-role admin
php com acl inspect-role --role=supervisor
```

Alias: `ls-role-policy`

---

### list-user-roles

Lista **todos** los usuarios con sus roles asignados. Incluye usuarios sin rol.

```bash
php com acl list-user-roles                    # todos los usuarios
php com acl list-user-roles --role=admin       # solo con rol "admin"
php com acl list-user-roles --role=null        # solo sin rol asignado
php com acl list-user-roles --format=json
```

Alias: `ls-user-roles`

> Ver `show-user-roles` para consultar un usuario específico.

---

### show-user-roles

Muestra los roles asignados a **un usuario específico**.

```bash
php com acl show-user-roles user@example.com
php com acl show-user-roles --email=user@example.com
```

Alias: `show-ur`

---

### assign-role

Asigna un rol a un usuario (INSERT en `user_roles`). No modifica `config/acl.php`.

```bash
php com acl assign-role --email=user@example.com --role=supervisor
php com acl assign-role --email=user@example.com --role=supervisor --dry-run
```

Requeridos: `--email`, `--role`  
Alias: `add-role`

---

### remove-role

Quita el rol asignado a un usuario (DELETE en `user_roles`). No modifica `config/acl.php`.

```bash
php com acl remove-role --email=user@example.com --role=supervisor
php com acl remove-role --email=user@example.com --role=supervisor --dry-run
```

Requeridos: `--email`, `--role`  
Aliases: `revoke-role`, `rm-role`

---

### list-sp

Lista todos los special permissions (capabilities) disponibles.

```bash
php com acl list-sp
php com acl list-sp --format=json
```

Alias: `ls-sp`

---

### grant-sp

Otorga un special permission individual a un usuario (INSERT en `user_sp_permissions`).

```bash
php com acl grant-sp --email=user@example.com --perm=impersonate
php com acl grant-sp --email=user@example.com --perm=impersonate --dry-run
```

Requeridos: `--email`, `--perm`

---

### revoke-sp

Revoca un special permission individual de un usuario (DELETE en `user_sp_permissions`).

```bash
php com acl revoke-sp --email=user@example.com --perm=impersonate
php com acl revoke-sp --email=user@example.com --perm=impersonate --dry-run
```

Requeridos: `--email`, `--perm`  
Alias: `rm-sp`

---

### list-user-sp

Lista los special permissions asignados individualmente a un usuario.

```bash
php com acl list-user-sp user@example.com
php com acl list-user-sp --email=user@example.com
```

Alias: `ls-user-sp`

---

### grant-tb

Agrega un permiso incremental de tabla a un usuario (actualiza `can_*` a 1).  
Shorthand: `read` = `show+list`, `write` = `create+update+delete`.

```bash
php com acl grant-tb --email=user@example.com --table=products --perm=create
php com acl grant-tb --email=user@example.com --table=products --perm=read
php com acl grant-tb --email=user@example.com --table=products --perm=write --dry-run
```

Requeridos: `--email`, `--table`, `--perm`  
Valores de `--perm`: `show | list | create | update | delete | show_all | list_all | read | write`

---

### revoke-tb

Quita un permiso incremental de tabla de un usuario (actualiza `can_*` a NULL).

```bash
php com acl revoke-tb --email=user@example.com --table=products --perm=delete
php com acl revoke-tb --email=user@example.com --table=products --perm=write --dry-run
```

Requeridos: `--email`, `--table`, `--perm`  
Alias: `rm-tb-perm`

---

### list-user-tb

Lista los permisos de tabla individuales de un usuario (tabla `user_tb_permissions`).

```bash
php com acl list-user-tb user@example.com
php com acl list-user-tb --email=user@example.com
```

Alias: `ls-user-tb`

---

### clear-tb ⚠ DESTRUCTIVO

Elimina **todos** los permisos de tabla individuales de un usuario para una tabla (DELETE en `user_tb_permissions`).

Requiere `--force` para ejecutarse. Sin él imprime el resumen y sale sin modificar datos.

```bash
# Previsualizar (recomendado)
php com acl clear-tb --email=user@example.com --table=products --dry-run

# Ejecutar
php com acl clear-tb --email=user@example.com --table=products --force
```

Requeridos: `--email`, `--table`  
Alias: `rm-tb`

---

### replace-tb ⚠ DESTRUCTIVO

Reemplaza **todos** los permisos de tabla de un usuario (replacement semantics). Los permisos no listados en `--perms` quedan en NULL.

Requiere `--force` para ejecutarse. Sin él imprime el resumen y sale sin modificar datos.

```bash
# Previsualizar (MUY recomendado)
php com acl replace-tb --email=user@example.com --table=products --perms=show,list,create --dry-run

# Ejecutar
php com acl replace-tb --email=user@example.com --table=products --perms=show,list,create --force
```

Requeridos: `--email`, `--table`, `--perms`  
`--perms`: lista CSV — `show,list,create,update,delete,show_all,list_all`

---

### add-deny

Agrega una deny rule a un usuario (INSERT en `user_deny_permissions`).  
**DENY tiene precedencia sobre cualquier ALLOW**.

```bash
php com acl add-deny --email=user@example.com --resource=products --action=delete
php com acl add-deny --email=user@example.com --resource=products --action=delete --dry-run
```

Requeridos: `--email`, `--resource`, `--action`  
Alias: `deny`  
Valores de `--action`: `show | list | create | update | delete | show_all | list_all`

---

### remove-deny

Elimina una deny rule de un usuario (DELETE en `user_deny_permissions`).

```bash
php com acl remove-deny --email=user@example.com --resource=products --action=delete
php com acl remove-deny --email=user@example.com --resource=products --action=delete --dry-run
```

Requeridos: `--email`, `--resource`, `--action`  
Alias: `rm-deny`

---

### list-deny

Lista las deny rules de un usuario (tabla `user_deny_permissions`).  
Precedencia: DENY > USER_GRANT > ROLE_GRANT.

```bash
php com acl list-deny user@example.com
php com acl list-deny --email=user@example.com
```

Alias: `ls-deny`

---

### can

¿Tiene el usuario ese permiso? (respuesta boolean).  
Para el chain completo usar `explain`.

```bash
# CRUD sobre recurso
php com acl can --email=user@example.com --perm=delete --resource=products

# Special permission del sistema
php com acl can --email=user@example.com --perm=impersonate

# Domain capability
php com acl can --email=user@example.com --perm=cashbox.open
```

Requeridos: `--email`, `--perm`  
`--resource` requerido para permisos CRUD; omitir para SPs y domain capabilities.  
Alias: `check`

---

### explain

Muestra el resolution chain completo para un permiso.  
Precedencia evaluada: **DENY > USER_GRANT > ROLE_GRANT**.

```bash
php com acl explain --email=user@example.com --perm=delete --resource=products
php com acl explain --email=user@example.com --perm=impersonate
php com acl explain --email=user@example.com --perm=cashbox.open
```

Requeridos: `--email`, `--perm`

---

### resolve

Muestra los permisos efectivos compilados de un usuario (todos los layers).

```bash
php com acl resolve user@example.com
php com acl resolve --email=user@example.com --only=sp
php com acl resolve --email=user@example.com --format=json
```

Alias: `effective`  
`--only`: filtrar sección — `roles | sp | tb | deny`

---

## Precedencia del sistema ACL

```
DENY  >  USER_GRANT  >  ROLE_GRANT
```

- **DENY**: filas en `user_deny_permissions` — bloquean sin excepción.
- **USER_GRANT**: permisos directos en `user_tb_permissions` y `user_sp_permissions`.
- **ROLE_GRANT**: permisos heredados del rol asignado en `user_roles`.

---

## Tablas de DB relacionadas

| Tabla | Propósito |
|-------|-----------|
| `sp_permissions` | Catálogo de capabilities disponibles (UNIQUE en `name`) |
| `roles` | Roles registrados |
| `user_roles` | Asignación de rol a usuario |
| `user_sp_permissions` | SPs individuales por usuario |
| `user_tb_permissions` | Permisos de tabla por usuario |
| `user_deny_permissions` | Deny rules explícitas por usuario |

---

Ver también: [ACL.md](../ACL.md) — arquitectura y DSL de configuración.
