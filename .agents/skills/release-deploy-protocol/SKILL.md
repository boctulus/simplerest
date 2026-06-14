---
name: release-deploy-protocol
description: Orchestrates the release lifecycle: git safety → production deployment → docker network reliability verification.
---

# SKILL_DEFINITION: release-deploy-protocol

## ACTIVATION (ENTRY GATE)

This SKILL MUST only be applied when ALL of the following conditions are true:

- Code is being deployed to any environment (staging, production)
- The deployment involves a Node.js application managed by PM2
- The deployment requires git operations and post-deployment verification

---

If these conditions are NOT met:

→ DO NOT APPLY this SKILL
→ STOP reading further instructions
→ Continue with other relevant SKILLs

## EXECUTION PLAN (MANDATORY)

STEP 1: Apply git safety protocol

TYPE: ACTION

ACTION: Enforce git-safety-protocol — decompose task into atomic sub-tasks, one commit per sub-task with conventional commit format (type(scope): description), no destructive git operations without explicit user approval

ON_FAILURE:
→ STOP
→ REPORT ERROR: Git safety protocol could not be applied

---

STEP 2: Execute production deployment

TYPE: ACTION

ACTION: Enforce production-deployment-protocol — verify deployment documentation exists, confirm SSH credentials, check port configuration in .env, verify application starts and responds

ON_FAILURE:
→ STOP
→ REPORT ERROR: Production deployment failed

---

STEP 3: Verify network reliability

TYPE: ACTION

ACTION: Enforce docker-network-reliability-guard — check container health (docker ps), verify no containers restarting, test service endpoints with curl, confirm resilient error handling in application code

ON_FAILURE:
→ STOP
→ REPORT ERROR: Network reliability check failed

---

STEP 4: Post-deployment verification

TYPE: CHECK

CHECK:
- All commits are atomic and follow convention
- Application is running (PM2 status) — **dos procesos**: `dashboard_firebase` + `printer-sidecar`
- Sidecar health check pasa: `curl http://localhost:7801/health` → `{"status":"ok"}`
- Logs show no runtime errors (`pm2 logs --lines 50`)
- Service endpoints respond correctly
- No container health issues (ECONNRESET, UND_ERR_SOCKET)
- Database connections are stable

ON_FAILURE:
→ STOP
→ REPORT ERROR: Post-deployment verification failed

## REQUIRES (HARD DEPENDENCIES)

These SKILLs MUST be active before this SKILL can execute:

- git-safety-protocol
- production-deployment-protocol
- docker-network-reliability-guard

If any are missing:
→ STOP
→ LOAD the missing SKILL
→ RESUME from the step that requires it

## SKILLS USED (loaded and applied during execution — NOT pre-conditions)

These SKILLs are invoked inline as steps of this SKILL:

- git-safety-protocol → applied in STEP 1
- production-deployment-protocol → applied in STEP 2
- docker-network-reliability-guard → applied in STEP 3

If a SKILL definition is not loaded:
→ STOP
→ LOAD the missing SKILL
→ RESUME from the step that requires it

## SKILL ORDER EXECUTION (ENFORCED SEQUENCE)

1. git-safety-protocol
2. production-deployment-protocol
3. docker-network-reliability-guard

## TRIGGERS

### ON_EVENT

EVENT: deploy_to_production
→ APPLY SKILL: release-deploy-protocol

---

### ON_EVENT

EVENT: deploy_to_staging
→ APPLY SKILL: release-deploy-protocol

---

### ON_COMPLETE

→ APPLY SKILL: skill-reviewer-protocol
