# ACL: problemas y mejoras

El ACL original, efectivamente ya tiene una forma parcial de “revocar” permisos, pero no como:

```txt id="i8vzsp"
deny semantics reales
```

sino como:

```txt id="m0ztj0"
permission replacement / override substitution
```

y esa diferencia es importante.

---

# Lo que realmente hace el sistema actual

En esta parte de el documentación:

```txt id="jlwmgc"
"user_tb_permissions" sobre-escriben los permisos propios del rol
```

y especialmente aquí:

```txt id="g55z7w"
"los permisos no se agregan individualmente sino todos los que se deseen setear a la vez y si se repitiera el proceso se sobre-escribiría lo que antes había"
```

el sistema YA implementa:

```txt id="wk13y7"
override replacement semantics
```

---

# Ejemplo real de el ACL

Supón:

```txt id="n1w3it"
Role: admin
```

tiene:

```txt id="x6ecww"
users.read
users.write
users.delete
```

Luego en:

```txt id="s6o8yb"
user_tb_permissions
```

defines:

```json id="q57jk7"
{
  "tb": "users",
  "user_id": 15,
  "can_show": true
}
```

Según el documentación:

```txt id="x6ehy2"
eso reemplaza los permisos previos
```

Entonces el usuario termina teniendo:

```txt id="nd4wb7"
ONLY:
users.show
```

y pierde:

```txt id="z1ivv0"
users.delete
users.update
users.create
```

---

# Entonces sí existía “revocación”

PERO:

era:

```txt id="d2t7xf"
implicit revocation by replacement
```

NO:

```txt id="x7j8t1"
explicit deny
```

---

# Diferencia arquitectónica

el modelo actual:

```txt id="jlwm3j"
override = replacement
```

Modelo enterprise moderno:

```txt id="ymukcf"
override = additional policy rule
```

---

# el sistema actual funciona así

```txt id="bjlwmw"
effective_permissions =
    user_override
    OR
    role_permissions
```

---

# Mientras deny semantics funciona así

```txt id="tqtqgq"
effective_permissions =
    resolve(
        allows,
        denies,
        precedence
    )
```

---

# Entonces técnicamente tú YA podías revocar

pero:

## ❌ de forma destructiva

porque:

```txt id="9qlgl2"
override reemplaza el set completo
```

---

# Problema de ese enfoque

No puedes expresar:

```txt id="4btt9m"
"mantén TODO excepto delete"
```

Debes redefinir TODO manualmente.

---

# Ejemplo

Si admin tiene:

```txt id="6r6f9l"
show
list
create
update
delete
```

y quieres revocar:

```txt id="2r3rxq"
delete
```

debes reconstruir:

```txt id="n4zqlz"
show
list
create
update
```

completo.

---

# Eso tiene varios problemas

## 1. Fragilidad

Si luego agregas:

```txt id="13v7mn"
archive
```

al rol admin:

el override queda desactualizado.

---

## 2. Drift

Los overrides divergen del rol base.

---

## 3. No composable

No puedes combinar reglas fácilmente.

---

## 4. No auditable semánticamente

No sabes:

```txt id="sd3hdn"
qué fue revocado
```

solo sabes:

```txt id="49k2p4"
cuál es el resultado final
```

---

# Entonces el ACL estaba en un punto intermedio

Tenías:

## ✔ replacement-based revocation

NO tenías:

## ❌ policy-based deny evaluation

---

# Arquitectónicamente esto es importante

Porque ahora puedes decidir:

---

# Opción A — mantener modelo replacement

Más simple.

```txt id="0vjlwm"
override reemplaza permisos efectivos
```

Pros:

* simple
* rápido
* deterministic

Contras:

* frágil
* poco composable
* difícil de mantener

---

# Opción B — migrar a deny semantics

Más enterprise.

```txt id="k7vflo"
ALLOW + DENY + precedence
```

Pros:

* composable
* auditable
* granular
* scalable
* policy-friendly

Contras:

* mucho más complejo
* necesita conflict resolution formal

---

# Mi recomendación para el ACL

Dado el sophistication level que ya tienes:

```txt id="zq0zhf"
folders
ownership
special permissions
hierarchy
overrides
```

yo NO seguiría con:

```txt id="hvbbva"
replacement semantics
```

porque ya empiezas a entrar en territory donde:

```txt id="m9e8ev"
effective permission composition
```

es inevitable.

---

# Lo correcto sería evolucionar hacia

```txt id="b0y1ls"
policy rules
```

tipo:

```php id="z6cjlwm"
ALLOW users.read
DENY users.delete
ALLOW invoices.*
DENY invoices.transfer
```

y luego resolver con precedencia formal.

---

# Respuesta corta exacta

Sí.

el ACL YA tenía revocación parcial mediante:

```txt id="5vjlwm"
override replacement semantics
```

pero NO tenía:

```txt id="efhmwt"
explicit deny semantics
```

que es el siguiente nivel enterprise.

---

Core del ACL:

src\framework\Security\Service\

Paquetes que extienden el ACL:

packages\boctulus\basic-acl\src\
packages\boctulus\fine-grained-acl\src

---

COMO PUEDO HACER ESA EVOLUCION?

---

docs\ACL.md