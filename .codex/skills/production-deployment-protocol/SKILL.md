---
name: production-deployment-protocol
description: Protocol for safely deploying Node.js applications to production using PM2, with strict safety checks and documented procedures.
---

# SKILL: Production Deployment Protocol

## Core Rules

- Always look for deployment documentation in:
  - `docs/production-deploy/` or
  - `docs/deploy/`

- SSH credentials:
  - Provided via `.txt` file
  - Never assume alternative access methods

## Assumptions (STRICTLY FORBIDDEN)

- Do NOT assume scripts in `scripts/` are valid for deployment unless documented
- Do NOT assume Cloud Functions deployment is required if not explicitly stated
- Do NOT assume database migrations were already applied

## Mandatory Checks

- ALWAYS verify application after deployment:
  - Check logs
  - Detect runtime errors
  - Confirm services are up and reachable

## Git Operations Security (MANDATORY)

- NEVER run `git clone` as root
- NEVER create project directories owned by root
- ALWAYS determine the target application user BEFORE cloning
- ALWAYS clone using the application user (e.g., `su - {user} -c "git clone ..."` or `sudo -u {user} git clone ...`)

Default Linux username for repository operations is 'boctulus' unless otherwise specified in deployment documentation.

### Why

Cloning as root creates files owned by root. The Node.js process runs as a non-root user and will fail on:
- `npm install` (cannot write node_modules)
- PM2 startup (cannot access app files)
- Any file write operation (logs, uploads, caches)

### Ownership Validation

After ANY git operation on the server, ALWAYS verify:

```bash
ls -la /path/to/project
```

If files are owned by root:
```bash
chown -R {appuser}:{appgroup} /path/to/project
```

→ MUST be done BEFORE running npm install or PM2 start

### Forbidden Patterns

❌ `git clone https://... /path/to/project` (when logged in as root)
❌ `cp -r` from root-owned temp dirs
❬ `npm install` executed as root on app directory

### Required Patterns

✅ `sudo -u {appuser} git clone https://... /path/to/project`
✅ `su - {appuser} -c "cd /path/to/project && git pull"`
✅ Ownership verification immediately after clone/pull

---

## SSH — Usuario correcto (CRÍTICO)

```bash
ssh boctulus@159.203.75.234
```

❌ **NUNCA** conectar como `root` para operaciones de deploy.
La app corre bajo `boctulus`. Usar root genera un PM2 daemon separado, crea archivos
con permisos incorrectos y causa crash loops en la app real.

## PM2 — Dos procesos activos (CRÍTICO)

Los procesos en producción corren bajo el usuario `boctulus`:

| Proceso | Script | Puerto |
|---|---|---|
| `index` | `index.js` | 3001 (app principal) |
| `printer-sidecar` | `printer-sidecar/server.js` | 7801 (PDF/Chromium) |

> El ecosystem.config.cjs define el nombre `dashboard_firebase`, pero el proceso
> fue registrado en PM2 como `index`. Usar siempre el nombre real del proceso.

### Re-deploy (caso habitual)

```bash
pm2 restart index
pm2 restart printer-sidecar
```

### Primera vez (procesos no existen)

```bash
cd /home/boctulus/mypos_nodejs
pm2 start ecosystem.config.cjs --only printer-sidecar --env production
pm2 start index.js --name index
pm2 save
```

### Después de cualquier cambio en la lista de procesos

```bash
pm2 save
```

### Verificación post-arranque

```bash
pm2 status
curl http://localhost:7801/health
```

Ambos procesos deben aparecer en `pm2 status` con estado `online`.

## Dependencias npm

Si `package.json` cambió tras el `git pull`:

```bash
npm install --production
```

> ⚠️ `pnpm` **no está disponible** en el servidor. Usar siempre `npm install`.

## Safety Constraints

- Do NOT restart Node server without explicit approval
- ALWAYS verify port configuration in `.env` before running services
- DO NOT ever overwrite existing `.env` without backup and confirmation.

## Complementary Skills

Read `./docs/safety-policies.md`

## Related Skills

This skill is orchestrated by:
- `release-deploy-protocol` — calls this skill as STEP 2 in the full release lifecycle

---

## REQUIRES (HARD DEPENDENCIES)

These SKILLs MUST be active before this SKILL can execute:

- git-safety-protocol

If any are missing:
→ STOP
→ LOAD them
→ RESTART execution

## SKILL ORDER EXECUTION (ENFORCED SEQUENCE)

Apply dependencies in this exact order:

1. git-safety-protocol

## TRIGGERS

None. Post-deployment steps (docker-network-reliability-guard) are orchestrated by release-deploy-protocol, not by this skill.
