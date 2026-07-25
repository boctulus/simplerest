---
name: gpt-blocking-decision-consult
description: Consult GPT-5.6 as an external auditor for blocking technical, architectural, product, operational, or implementation decisions. Supports problem reframing, targeted context expansion, and bounded multi-round consultation.
---

# SKILL: gpt-blocking-decision-consult

## PURPOSE

Provide a controlled and cost-bounded mechanism for consulting GPT when Claude encounters a genuinely blocking or high-impact decision.

The consultation must audit both:

1. Whether the question, assumptions, and decision scope are correctly framed.
2. Whether the proposed decision is sound within the validated scope.

GPT is not limited to approving or rejecting the alternatives presented. It may determine that:

* The question is too narrow.
* The decision is premature.
* Important context is missing.
* The alternatives address the wrong problem.
* The real issue exists at a higher architectural, product, operational, or project-planning level.
* The current task should be reframed, deferred, split, or abandoned.

The protocol allows a small number of controlled follow-up rounds when additional context or reframing is justified, while preventing unbounded consultation loops.

---

## ACTIVATION GATES

### INVOKE when ALL of the following are true

* A decision is genuinely blocking, high-impact, or expensive to reverse.
* Claude cannot resolve it confidently from existing project evidence.
* At least one trigger condition below is met.
* The consultation budget for the task is not exhausted.

A decision is genuinely blocking when proceeding without resolving it would create a meaningful risk of:

* Rework across multiple files or modules.
* Data loss or migration failure.
* Architectural inconsistency.
* Security or authorization defects.
* Breaking public or internal contracts.
* Invalidating later planned work.
* Deploying an operationally unsafe change.
* Implementing the wrong product behavior.
* Creating significant maintenance debt.
* Committing to an expensive or difficult-to-reverse direction.

### TRIGGER CONDITIONS

At least one is required:

* Global or cross-module architecture change.
* Bounded-context, ownership, or responsibility change.
* Domain model or persistence decision.
* Data migration or irreversible transformation.
* Multi-tenant isolation or authorization decision.
* Integration affecting multiple systems or modules.
* Public API, compatibility, or versioning decision.
* Deployment, infrastructure, or operational decision with shared-system impact.
* Product behavior that changes workflows, permissions, billing, onboarding, or user-visible semantics.
* Choice between incompatible implementation strategies.
* Decision whose correctness depends on assumptions Claude cannot validate confidently.
* Conflict between the current task and the persistent work plan.
* Evidence that the requested task may be solving the wrong problem.
* Any other decision with high rework cost or broad consequences.

### DO NOT INVOKE for

* Local refactoring with obvious behavior preservation.
* Naming variables, functions, classes, files, or routes.
* Minor internal API choices.
* Formatting, cosmetic changes, or code-style preferences.
* Straightforward implementation details already determined by an accepted plan.
* Questions answerable directly from tests, documentation, repository evidence, or established project conventions.
* Situations where consultation would merely duplicate Claude's reasoning without introducing an independent audit.

---

## CONSULTATION BUDGET

### Default budget

Each task starts with:

```text
GPT_CALLS_THIS_TASK = 0
GPT_BASE_BUDGET = 2
GPT_HARD_LIMIT = 5
```

The normal expectation is one consultation.

A second consultation is allowed when the first response:

* Requests essential missing context.
* Reframes the decision.
* Rejects the proposal and a revised proposal must be reviewed.
* Identifies a contradiction that can be resolved with repository or plan evidence.

### Budget extension

GPT may request additional consultation rounds using:

```text
BUDGET_REQUEST: NONE | +1 | +2
BUDGET_REASON: <specific reason>
```

Claude may grant the request only when:

* The issue remains genuinely blocking.
* The next round has a specific new purpose.
* New evidence, context, or a revised proposal will be provided.
* The next query will not merely repeat the previous question.
* `GPT_CALLS_THIS_TASK < GPT_HARD_LIMIT`.

A budget request is advisory. Claude decides whether it is justified.

Do not ask the user for confirmation solely to grant an internal consultation-budget extension.

### Hard limit

Never exceed five GPT calls for one task.

Before every call:

```text
if GPT_CALLS_THIS_TASK >= GPT_HARD_LIMIT:
    stop consulting
    proceed with Claude's best judgment
    document unresolved uncertainty
```

After every successful call:

```text
GPT_CALLS_THIS_TASK++
```

The hard limit cannot be increased by GPT or Claude.

Only the human user may explicitly authorize a new consultation cycle after the hard limit is reached.

---

## CONTEXT SELECTION

The consultation must provide enough context for GPT to evaluate both the proposed answer and the framing of the problem.

Do not blindly send the entire repository or entire work plan.

Include the smallest context package that preserves the relevant constraints and relationships.

### Required context

Every consultation must include:

* Project and technology context.
* Current task or blocking situation.
* Decision being considered.
* Evidence already inspected.
* Relevant constraints.
* Proposed alternatives, when alternatives exist.
* Claude's current preferred direction, when one exists.
* Consequences of making the wrong decision.

### Work-plan context

Include work-plan context when any of the following is true:

* The decision may affect later phases.
* The task belongs to a multi-step migration or refactor.
* There are dependencies between current and future work.
* The proposed change may invalidate previous decisions.
* The persistent plan defines scope, sequencing, invariants, or completion criteria.
* GPT cannot evaluate the decision correctly without understanding the larger objective.

Provide:

```text
WORK PLAN
Plan reference: <path or persistent plan identifier>
Current phase: <phase or task>
Relevant objectives:
- <objective>
Relevant dependencies:
- <dependency>
Relevant future steps:
- <future step affected by this decision>
```

Include only the relevant plan excerpt or a faithful summary.

A file path by itself is not sufficient unless the consultation mechanism can read that file. The query must contain the information GPT needs.

### Repository evidence

When relevant, summarize concrete evidence:

```text
EVIDENCE
- <schema or migration fact>
- <existing implementation fact>
- <test result>
- <contract or compatibility requirement>
- <previous ADR or project decision>
```

Distinguish verified facts from assumptions.

---

## EFFORT SELECTION

| Level    | Use when                                                                                                                                                                                                    |
| -------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `medium` | Two or three known alternatives, bounded cross-module changes, component boundaries, ordinary schema evolution, product workflow decisions                                                                  |
| `high`   | Distributed systems, critical migrations, multi-tenant isolation, security-sensitive authorization, data consistency, shared infrastructure, major reframing, or decisions affecting multiple future phases |

Do not use `minimal`, `low`, or `xhigh`.

Use `high` when GPT must evaluate whether the problem itself is framed at the correct level.

---

## QUERY STRUCTURE

Use the following structure.

Sections marked optional may be omitted when genuinely irrelevant. Do not force artificial alternatives when the problem is exploratory or may require reframing.

```text
PROJECT CONTEXT
<Concise description of the project, stack, current state, and relevant architectural or operational constraints>

CURRENT TASK
<What Claude is currently trying to accomplish>

WORK PLAN
Plan reference: <path or identifier>
Current phase: <phase>
Relevant objectives:
- <objective>
Relevant dependencies or future consequences:
- <dependency or consequence>

KNOWN FACTS
- <verified fact>
- <verified fact>

ASSUMPTIONS
- <unverified assumption>
- <unverified assumption>

BLOCKING QUESTION
<The decision or uncertainty currently preventing progress>

CURRENT FRAMING
<Why Claude currently believes this is the decision that must be made>

ALTERNATIVES
A: <option A>
B: <option B>
C: <option C>

CURRENT PREFERENCE
<Claude's preferred option and brief reason, or NONE>

CONSTRAINTS
- <constraint>
- <constraint>

FAILURE IMPACT
<What can go wrong if the framing or decision is incorrect>

AUDIT INSTRUCTIONS

Act as an independent senior auditor.

First evaluate whether the blocking question is correctly framed and whether it addresses the real problem at the correct scope.

Do not assume the listed alternatives are exhaustive or that the requested decision should be made now.

You may:
- Approve or reject the proposal.
- Challenge the assumptions.
- Reframe the question.
- Identify a higher-level problem.
- Recommend a different alternative.
- Request specific missing context.
- Recommend splitting, deferring, or abandoning the current task.
- Request an additional consultation round when justified.

A high decision score must not override a framing problem.

Return the structured response below.
```

When no meaningful alternatives have been identified, use:

```text
ALTERNATIVES
Not yet established. Determine whether alternatives should be generated only after reframing or obtaining more context.
```

---

## RESPONSE FORMAT

GPT must return the following structure:

```text
QUESTION_ASSESSMENT: SOUND | TOO_NARROW | MISFRAMED | PREMATURE | INSUFFICIENT_CONTEXT
SCOPE_ASSESSMENT: LOCAL | MODULE | CROSS_MODULE | PROJECT | CROSS_SYSTEM
PRIMARY_FINDING:
<Most important conclusion, including any issue that invalidates the current framing>

VERDICT: APPROVED | APPROVED_WITH_NOTES | REJECTED | REFRAME_REQUIRED | MORE_CONTEXT_REQUIRED | DEFER
SCORE: X/10 | NOT_APPLICABLE

BLOCKERS:
- <blocking issue>

RISKS:
- <important non-blocking risk>

RECOMMENDED_FOCUS:
<The problem or decision Claude should address next>

RECOMMENDED_DECISION:
<Recommended option or direction, or NOT_YET_DECIDABLE>

MISSING_CONTEXT:
- <specific missing fact or evidence>

FOLLOW_UP_QUESTIONS:
- <question that would materially change the decision>

BUDGET_REQUEST: NONE | +1 | +2
BUDGET_REASON:
<Why another GPT consultation would provide new value>

NOTES:
- <additional observations>
```

### Response rules

* `SCORE` evaluates the proposed decision only within a sound framing.
* When `QUESTION_ASSESSMENT` is not `SOUND`, GPT must not use the score as the main closure signal.
* Use `NOT_APPLICABLE` when the decision cannot yet be evaluated.
* Limit `FOLLOW_UP_QUESTIONS` to a maximum of three.
* Questions must request concrete missing evidence, not broad discussion.
* `REFRAME_REQUIRED` means the current question must not be answered as-is.
* `MORE_CONTEXT_REQUIRED` means GPT lacks information necessary for a responsible decision.
* `DEFER` means the decision should be postponed until another task, dependency, or investigation is completed.
* Do not omit a higher-level danger merely because the local proposal is technically valid.

---

## EXECUTION PROTOCOL

### Step 1: Identify the blocker

Determine:

* What is blocking progress.
* Whether the blocker is a decision, missing evidence, or ambiguous requirement.
* Which trigger condition applies.
* Whether consultation adds independent value.

Do not invoke GPT merely because a decision is difficult.

### Step 2: Check budget

Confirm:

```text
GPT_CALLS_THIS_TASK < GPT_HARD_LIMIT
```

Record the current call number.

### Step 3: Build the context package

Collect:

* Relevant project context.
* Current task.
* Relevant work-plan excerpt.
* Verified repository evidence.
* Assumptions.
* Current framing.
* Alternatives, if any.
* Failure impact.

Do not conceal uncertainty.

### Step 4: Select effort

Choose `medium` or `high`.

### Step 5: Execute consultation

Do not run `ask_gpt.py` through Bash or PowerShell.

Do not ask the user for confirmation.

Write:

```text
C:\Users\jayso\.claude\gpt-consult.json
```

using the Write tool:

```json
{
  "question": "<constructed consultation query>",
  "effort": "<medium|high>",
  "task_id": "<stable task or plan identifier>",
  "round": 1
}
```

If the hook currently accepts only `question` and `effort`, omit unsupported fields and include the task identifier and round inside the question text.

The PostToolUse hook:

* Calls GPT-5.6.
* Injects the response through `additionalContext`.
* Saves the raw response to:

```text
C:\Users\jayso\.claude\gpt-consult-response.txt
```

Wait for the hook response to appear before interpreting the consultation.

Increment:

```text
GPT_CALLS_THIS_TASK++
```

### Step 6: Interpret the framing assessment

#### `SOUND`

Proceed to evaluate `VERDICT`.

#### `TOO_NARROW` or `MISFRAMED`

* Stop treating the original question as the active decision.
* Compare GPT's reframing with repository and plan evidence.
* Create a revised blocking question.
* Execute another consultation only when independent review of the reframed question is useful.
* Do not record the original proposal as approved merely because it had a high local score.

#### `PREMATURE`

* Identify the dependency or investigation that must happen first.
* Update the task sequence when appropriate.
* Do not force a decision.

#### `INSUFFICIENT_CONTEXT`

* Gather the requested concrete evidence.
* Ask GPT again only if the new evidence can materially change the answer.
* Do not repeat the same query without new information.

### Step 7: Interpret the verdict

#### `APPROVED`

* Proceed with the proposal.
* Preserve any important risks or conditions in the implementation notes.
* Register the final decision when registration criteria are met.

#### `APPROVED_WITH_NOTES`

* Determine which notes are implementation conditions and which are advisory.
* Incorporate material conditions.
* Proceed unless a note reveals a contradiction with verified project evidence.

#### `REJECTED`

* Do not continue with the rejected proposal.
* Produce a materially revised proposal or alternative.
* A second consultation is allowed when it reviews new reasoning or evidence.

#### `REFRAME_REQUIRED`

* Replace the active decision with the recommended focus.
* Update the work plan or task description when the scope has changed materially.
* Consult again only with the reframed question.

#### `MORE_CONTEXT_REQUIRED`

* Gather the specific missing facts.
* Avoid speculative implementation.
* Consult again with the newly gathered evidence when justified.

#### `DEFER`

* Record why the decision is deferred.
* Identify the prerequisite or trigger for reopening it.
* Continue with unaffected work when possible.

### Step 8: Decide whether another round is justified

Another consultation is allowed only when at least one of these is true:

* New repository evidence was obtained.
* The question was materially reframed.
* A rejected proposal was materially redesigned.
* A contradiction between GPT and project evidence must be resolved.
* GPT requested additional context that can now be supplied.
* The decision remains high-impact and unresolved.

Do not use another round to ask GPT to repeat, defend, or cosmetically restate the same answer.

### Step 9: Closure

Close the consultation cycle when:

* The framing is sound.
* The decision is actionable.
* No unresolved blocker remains.
* Further GPT calls would not introduce new evidence or a materially different evaluation.

The decision does not require an arbitrary score threshold when the qualitative findings are clear.

As a guideline:

```text
SCORE >= 8
AND QUESTION_ASSESSMENT = SOUND
AND no BLOCKERS
```

normally supports approval, but does not automatically override contradictory evidence.

Claude remains responsible for reconciling GPT's advice with repository facts.

---

## CONSULTATION FLOW

```text
START
  |
  +-- Is the issue genuinely blocking or high-impact?
  |     |
  |     +-- NO --> Use Claude's judgment --> DONE
  |
  +-- Is consultation budget available?
  |     |
  |     +-- NO --> Use best judgment --> Document uncertainty --> DONE
  |
  +-- Build context package
  |     |
  |     +-- Include relevant work-plan context when needed
  |
  +-- Consultation round
        |
        +-- SOUND
        |     |
        |     +-- APPROVED / APPROVED_WITH_NOTES
        |     |       --> Reconcile notes --> Register if required --> DONE
        |     |
        |     +-- REJECTED
        |             --> Revise proposal
        |             --> Optional new-evidence round
        |
        +-- TOO_NARROW / MISFRAMED
        |       --> Replace original question
        |       --> Update task or plan scope
        |       --> Optional reframed round
        |
        +-- INSUFFICIENT_CONTEXT
        |       --> Gather specific evidence
        |       --> Optional evidence-backed round
        |
        +-- PREMATURE / DEFER
                --> Record prerequisite
                --> Continue unaffected work or stop task
```

---

## RECORDING CONSULTATIONS AND DECISIONS

A consultation and a final project decision are not the same artifact.

### Consultation log

Save every completed consultation cycle under:

```text
docs/consultations/
```

Suggested structure:

```text
docs/consultations/
  index.md
  YYYY-MM-DD-<task-id>-gpt-consult.md
```

Format:

```markdown
# GPT Consultation: <Title>

**Date:** YYYY-MM-DD
**Task:** <task or plan reference>
**Calls used:** X
**Final status:** RESOLVED | REFRAMED | DEFERRED | UNRESOLVED

## Original Blocking Question
<Original question>

## Relevant Context
<Concise context and work-plan relationship>

## GPT Findings
- **Question assessment:** <value>
- **Scope assessment:** <value>
- **Verdict:** <value>
- **Score:** <value>
- **Primary finding:** <summary>

## Consultation Evolution
1. <Round and result>
2. <Round and result>

## Claude Reconciliation
<Where GPT agreed or conflicted with repository evidence>

## Outcome
<What changed, what will be done next, or why the matter was deferred>

## Remaining Uncertainty
<None or unresolved concerns>
```

### ADR registration

Create or update an ADR under:

```text
docs/decisions/
```

only when the result establishes a durable project-level decision, such as:

* Architecture.
* Domain ownership.
* Persistence strategy.
* Public contracts.
* Security model.
* Cross-module behavior.
* Infrastructure topology.
* High-cost migration strategy.

Do not create an ADR for every implementation-level consultation.

ADR statuses may include:

```text
PROPOSED
ACCEPTED
SUPERSEDED
DEFERRED
REJECTED
```

Do not mark a decision `CLOSED` merely because GPT returned an answer.

Only the human user or established project governance rules determine whether an accepted decision may later be reopened.

---

## CRITICAL RULES

1. Audit the question before auditing the answer.
2. GPT may reject the framing even when one listed alternative is locally valid.
3. A high score never cancels a framing problem or blocker.
4. Include relevant work-plan context whenever sequencing or future phases matter.
5. Do not send the entire plan when a precise excerpt is sufficient.
6. Do not force alternatives when the correct alternatives are not yet known.
7. Every additional round must contain new evidence, a reframed question, or a materially revised proposal.
8. Default to one consultation; use additional rounds only when justified.
9. Never exceed five GPT calls for one task.
10. GPT may request more budget but cannot grant it.
11. Claude must reconcile GPT advice with verified repository evidence.
12. Do not register a durable decision before the framing is sound.
13. GPT is an external auditor, not the final authority.
14. Do not hide assumptions or present them as verified facts.
15. Do not continue implementing a locally approved solution when GPT identifies a credible higher-level blocker.
16. Do not ask the user for confirmation solely to execute an allowed consultation round.
17. Use only `medium` or `high` effort.
18. Preserve the consultation response for auditability.
19. Fix encoding before installing or executing this skill.
20. When the hard limit is reached, stop consulting and document the remaining uncertainty.
