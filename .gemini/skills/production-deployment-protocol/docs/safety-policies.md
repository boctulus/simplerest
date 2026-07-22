# Safety Policies

## Problema que resuelve

Cuando el agente hace SSH como `root` para ejecutar `git pull` y reiniciar PM2, el proceso Node.js queda corriendo bajo `root`. Si el PM2 real del servidor corre bajo otro usuario (ej: `boctulus`), quedan dos instancias compitiendo en el mismo puerto. El usuario no-root no puede matar el proceso root con `pkill`, y el servidor queda en estado inconsistente.

---

## Reglas OBLIGATORIAS

### REGLA 1 — SSH para PM2/Node NUNCA como root

```
❌ ssh root@HOST "pm2 restart ..."
✅ ssh boctulus@HOST "pm2 restart ..."
```

El `git pull` puede hacerse como root si es necesario, pero **el restart de PM2 siempre debe ejecutarse con el usuario propietario del proceso**.

### REGLA 2 — Verificar proceso existente ANTES de iniciar PM2

Antes de cualquier `pm2 start` / `pm2 restart` / `pm2 delete`, ejecutar:

```bash
ssh boctulus@HOST "ss -tlnp | grep <PORT>; ps aux | grep node | grep -v grep"
```

Si aparece un proceso corriendo como `root` en el puerto:
1. **Advertir al usuario** con el PID y usuario propietario
2. **Pedir confirmación explícita** antes de matarlo
3. Solo si el usuario aprueba, ejecutar via `ssh root@HOST "kill -9 <PID>"`

### REGLA 3 — Secuencia correcta de deploy

```
1. ssh root@HOST     "cd <PROJECT_DIR> && git pull"
2. ssh boctulus@HOST "pm2 list"                           # ver estado actual
3. ssh boctulus@HOST "pm2 delete all"                     # detener correctamente
4. ssh boctulus@HOST "cd <PROJECT_DIR> && pm2 start index.js --name <NAME>"
5. ssh boctulus@HOST "pm2 logs --lines 30 --nostream"    # verificar arranque
```

### REGLA 4 — Verificación post-deploy

```bash
ssh boctulus@HOST "pm2 list"
# Confirmar: status=online, user=boctulus, restarts bajos
```

---

## Checklist antes de ejecutar deploy

- [ ] ¿Voy a usar `boctulus` para PM2? (no root)
- [ ] ¿Verifiqué con `ss -tlnp | grep PORT` que no hay proceso root en el puerto?
- [ ] ¿Ejecuté `pm2 list` como `boctulus` antes de reiniciar?
- [ ] ¿Después del restart, `pm2 list` muestra `online` y usuario `boctulus`?

---

## Detección y limpieza de proceso root huérfano

Si se detecta un proceso Node.js corriendo como root en el puerto productivo:

```bash
# 1. Identificar PID
ssh boctulus@HOST "ps aux | grep node"

# 2. Advertir al usuario y pedir confirmación antes de matar
# 3. Solo si aprueba:
ssh root@HOST "kill -9 <PID>"

# 4. Verificar puerto libre
ssh boctulus@HOST "ss -tlnp | grep <PORT>"

# 5. Iniciar correctamente
ssh boctulus@HOST "cd <PROJECT_DIR> && pm2 start index.js --name <NAME>"
```
