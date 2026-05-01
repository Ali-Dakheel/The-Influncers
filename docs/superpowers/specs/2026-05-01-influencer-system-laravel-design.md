# The Influencer System — Laravel Backend Design

**Date:** 2026-05-01
**Status:** Approved (brainstorming)
**Audience:** Personal exploration / foundation phase
**Project:** The Influencer System — Raincode-built AI-native influencer marketing platform (board-signed 2026-05-01)

---

## 1. Goal

Build the entire Laravel backend of the Influencer System as a sequenced set of dependency-ordered sprints. Each sprint produces a shippable, testable slice. Frontend (Next.js) is built afterward as a final phase. AI features (Phases 2A and 2B from the pitch) are explicitly out of scope for this design — they will get their own design doc later.

The Laravel side covers ~30 features from the signed feature list. AI features (Health Score, Brand Safety Scanner, Matching Engine, Performance Prediction, Sales Intelligence, AI Agent, Trend Scanner, Mood Board compliance, Amplification Network, AI Video Reports, Paid Ads Boosting, AI Draft Reviewer, AI Chatbot) are deferred to a future FastAPI service.

---

## 2. Non-Goals

- **No Next.js work during sprints 0–9.** Frontend is sprint 10 only.
- **No AWS, no paid SaaS.** All local: Postgres, log-driver email, database queue, local filesystem, Stripe test mode only.
- **No AI features.** Schemas leave room for them (typed columns, outcome records) but no integrations.
- **No production deployment.** Personal exploration; runs locally.
- **No NestJS pivot.** Stack stays Laravel + Next.js until/unless re-decided.

---

## 3. Tech Stack

| Layer | Choice | Why |
|---|---|---|
| Backend framework | Laravel 11 (from Maestro PR #78 stateless API kit) | API-only, Sanctum auth pre-wired, scaffolded |
| Database | PostgreSQL (local) | Matches pitch deck; pgvector-ready for future AI |
| Auth | Laravel Sanctum (already scaffolded) | Stateless tokens for API-first build |
| Queue | `database` driver | No Redis needed; runs locally with `queue:work` |
| Email | `log` driver | Writes to `storage/logs/laravel.log` for dev |
| File storage | Local filesystem (`public` disk) | Zero setup; switch to S3 later when budget exists |
| Payments | Stripe Connect (test mode) | Free; real API; introduced in Sprint 7 |
| API docs | Scribe (already in kit) | Auto-generated from PHP attributes; served at `/docs` |
| Tests | PHPUnit 12 (already in kit; Boost explicitly mandates over Pest) | TDD per feature; `php artisan test --compact` |
| Linting | Pint (already in kit) | Standard Laravel formatter |
| Dev MCP | Laravel Boost | DB introspection, log access, error tracing for Claude Code |

---

## 4. Architecture

**Pattern:** Traditional Laravel (Coolify-style) with strong Action-pattern separation. Not DDD modules.

```
api-project/app/
├── Actions/           Single-purpose invokable business operations
├── Http/
│   ├── Controllers/   Thin — validate via FormRequest, dispatch to Action
│   ├── Requests/      Form request validation per write endpoint
│   ├── Resources/     API response shapes (no raw model serialization)
│   └── Middleware/
├── Models/
├── Policies/          One per model
├── Services/          Multi-step coordination (3+ steps or external systems)
├── Jobs/              Anything >100ms or external request
├── Notifications/     Email + push (push is stub until FCM)
├── Events/            State transitions: ApplicationAccepted, DraftApproved, etc.
└── Listeners/         Side effects of events (notify, log, archive)
```

**Rules:**
- Controllers do not contain business logic. They validate, authorize, and call exactly one Action or Service.
- Every write endpoint has a Form Request. Every read endpoint validates via route model binding + policy.
- Every model has a Policy. Authorization via `$this->authorize(...)` in controllers, never inline checks.
- Every model has a Resource. Never return raw models from API endpoints.
- Every state transition fires an Event. Listeners handle side effects (notifications, audit logs, attribution updates).

---

## 5. Data Model Principles (Flywheel-Ready)

Even though AI is out of scope here, the pitch promises "all stored as typed columns from Sprint 1." The schema must support the future Matching Engine and Performance Memory without refactor.

**Typed columns from day one:**
- `campaigns`: `category` (enum), `market_id` (FK), `platforms` (jsonb array of enums), `format` (enum), `budget_cents`, `objective` (enum)
- `users`: `country_id` (FK), `role` (enum)
- `creators` (1:1 with users where role=influencer): `audience_size`, `niche`, `engagement_rate_cached`
- `applications`: `status` (enum), `pitched_price_cents`, `applied_at`, audit columns
- `drafts`: `revision_number`, `submitted_at`, `approved_at`, `file_path`
- `outcomes` (created Sprint 4, populated Sprint 4 onwards): `campaign_id`, `creator_id`, `reach`, `engagement`, `conversions`, `cost_per_result_cents`, `final_post_url`, `recorded_at`

**`OutcomeRecord` table** is the seed of the future Performance Memory. Every campaign completion writes one. We don't read it for matching yet — but it accumulates from Sprint 4 onwards so AI Phase 2A has months of real data when it ships.

---

## 6. Sprint Plan

Each sprint is one feature branch off `main`. Definition of Done at the bottom of this section applies to every sprint.

### Sprint 0 — Foundation & Tooling

**Claude Code workflow setup** (per §9):
- Verify Laravel Boost MCP is wired in `.claude/settings.json` (already installed)
- Write root `/CLAUDE.md` (high-level overview, points to subfolder CLAUDE.md files, references this spec)
- Write `/api-project/CLAUDE.md` (Laravel rules: PHPUnit TDD, Action pattern, never edit migrations, run Pint before commit)
- Configure `.claude/settings.json` allowlist (artisan, pest, pint, composer, npm, npx — see §9.2)
- Add hook: PreToolUse on Edit blocks changes to existing migration files

**Laravel scaffolding:**
- Switch DB from SQLite → PostgreSQL, update `.env.example`
- Verify Scribe + PHPUnit + Pint baseline still passes
- Initialize Bruno collection at `api-project/bruno/`
- Establish `Actions/` directory pattern with one example (`Actions/Demo/PingAction.php`)
- Confirm fresh `php artisan migrate:fresh --seed` works against Postgres

### Sprint 1 — Identity Foundation
- `users` table: `role` enum (`brand`, `influencer`, `agency`, `admin`), `country_id` FK
- `countries` table seeded with EN-speaking + SE markets at minimum
- Sanctum token auth (already scaffolded — verify + extend)
- Email verification (log driver)
- Password reset (log driver)
- Role-differentiated registration endpoints (or one with `role` field)
- `UserPolicy`, `UserResource`
- PHPUnit tests for every endpoint

### Sprint 2 — Campaigns & Briefs
- `campaigns` table with all flywheel columns from §5
- `campaign_states` enum: `draft`, `published`, `closed`, `paused`, `completed`
- Mood board fields: title, description, file uploads (no AI compliance yet)
- Brand-only CRUD: create/update/publish/close/pause campaigns
- `CampaignPolicy` (only owning brand can mutate; influencers can read published)
- `CreateCampaign`, `PublishCampaign`, `PauseCampaign`, `CloseCampaign` Actions
- PHPUnit tests + Bruno

### Sprint 3 — Applications & Selection
- `applications` table with status enum, pitched price, audit timestamps
- Influencer browse endpoint (filterable: market, category, platforms, budget range)
- Influencer apply endpoint (with pitch message, proposed price)
- Brand list applicants endpoint
- Brand accept/reject application endpoint
- `ApplicationPolicy`
- `ApplyToCampaign`, `AcceptApplication`, `RejectApplication` Actions
- `ApplicationAccepted` Event (no listener yet — Sprint 5 wires it)

### Sprint 4 — Drafts & Approval
- `drafts` table: `application_id`, `revision_number`, `file_path`, `status`, audit cols
- `outcomes` table created (per §5)
- Influencer upload draft endpoint
- Brand review endpoint: approve / request changes (with notes)
- Final post URL submission endpoint (records `final_post_url` on outcome)
- Campaign completion → write `OutcomeRecord`
- `DraftSubmitted`, `DraftApproved`, `DraftChangesRequested`, `CampaignCompleted` Events
- `SubmitDraft`, `ApproveDraft`, `RequestDraftChanges`, `CompleteCampaign` Actions

### Sprint 5 — Notifications & Scheduled Jobs
- Configure database queue, document `php artisan queue:work` workflow
- Email notifications wired to all events from sprints 1–4 (log driver)
- Push notification class scaffolded (stub send method — real FCM later)
- Scheduled commands:
  - `campaigns:remind-deadlines` (runs daily, fires reminder for drafts due in 24h)
  - `campaigns:close-expired` (auto-close past deadline)
- **Emergency stop endpoint** (brand-scoped): pauses all active campaigns for that brand, fires notifications via every channel
- Date-change auto-notify on campaign update

### Sprint 6 — Reputation & Creator OS
- `portfolios` table (linked to influencer): past collabs (manually entered for now), content style tags, audience data fields
- `price_lists` table: per-platform, per-format pricing + JSON for package deals
- `ratings` table: 1-5 stars + text, attributed to brand, posted post-completion
- Influencer income summary endpoint (reads completed campaigns + their payment status — payment fields stubbed until Sprint 7)
- Calendar endpoint (list of campaigns + deadlines for the user)
- `RateInfluencer`, `UpdatePortfolio`, `UpdatePriceList` Actions

### Sprint 7 — Payments & Invoicing
- Stripe Connect onboarding for influencers (Express accounts, test mode)
- Brand payment intent on campaign acceptance (held in escrow conceptually)
- Release payment to influencer on campaign completion
- `invoices` table; auto-generate PDF (DomPDF) on payment
- Sales attribution: `sales_rep_id` on brand records, all revenue tagged
- Multi-currency: store original + in cents
- Budget goals: brand monthly spend tracking, influencer income targets

### Sprint 8 — Reporting & Admin
- Reporting endpoints with filters (influencer/platform/country/brand/date range)
- Aggregations: reach, engagement, conversions, cost-per-result
- PDF export endpoint
- Country admin role: country-scoped views (admin can only see their country's data)
- Sales account dashboard: per-rep revenue, top accounts, churn signals

### Sprint 9 — Polish & "Wow" Features
- **Last-minute marketplace**: campaigns flagged urgent, expire-soon filter, instant-accept flow
- **Video pitch system**: influencer-initiated, video upload to brand inbox, brand review/accept
- **Follow feed**: users follow brands/influencers, activity stream endpoint
- **Multi-language**: EN + SV via Laravel translation files; `Accept-Language` header support
- **Content archiving**: completed campaigns + drafts move to archive table after 90 days

### Sprint 10 — Frontend (Next.js)
- shadcn/ui + Tailwind setup
- Wire all Bruno-tested endpoints
- Pages per persona: brand, influencer, agency, admin
- Auth flow integration with Sanctum tokens

---

## 7. Per-Sprint Definition of Done

Every sprint must satisfy:

- [ ] All new PHPUnit tests pass; no existing tests broken
- [ ] Scribe regenerated; `/docs` reflects new endpoints
- [ ] Bruno collection updated with example requests/responses
- [ ] Pint formatter passes (`composer pint`)
- [ ] Migrations are reversible (`migrate:rollback` works)
- [ ] Seeders updated; `php artisan migrate:fresh --seed` produces a usable demo dataset
- [ ] `CLAUDE.md` updated with any new patterns introduced
- [ ] Sprint branch merged to `main` via PR (even though solo, for the workflow practice)

---

## 8. Sprint Workflow (Per-Sprint Mechanics)

1. Branch from `main`: `git checkout -b sprint-N-name`
2. For each feature in the sprint:
   - Write PHPUnit test → run → red
   - Implement (Action / Migration / Resource / Policy / etc.)
   - Run test → green
   - Refactor
3. Update Bruno collection
4. Run `php artisan scribe:generate`
5. Update `CLAUDE.md` if patterns changed
6. Run `composer pint`
7. Run full test suite
8. Verify Definition of Done checklist
9. Commit, push, PR, merge

---

## 9. Claude Code Workflow

This section codifies how we use Claude Code throughout the build, derived from the [official best practices](https://code.claude.com/docs/en/best-practices).

### 9.1 CLAUDE.md hierarchy (monorepo)

Claude auto-loads child `CLAUDE.md` files when working in subdirectories. We exploit that:

- `CLAUDE.md` (repo root) — high-level project overview, points to subfolder CLAUDE.md files, references the design spec, defines repo-wide conventions (commit style, branch naming).
- `api-project/CLAUDE.md` — Laravel-specific rules: PHPUnit TDD, Action pattern, Form Request + Resource + Policy per endpoint, never edit existing migrations, run Pint before commit, generate Scribe after each sprint.
- `frontend/CLAUDE.md` — Next.js rules (written in Sprint 10).
- `CLAUDE.local.md` (gitignored) — personal overrides if needed.

**Rule:** Keep each file short and scannable. If a rule is being ignored, the file is probably too long. Convert non-negotiable rules to hooks instead of repeating them.

### 9.2 Settings & permissions (`.claude/settings.json`)

Allowlist commands we run dozens of times per sprint so we stop hitting permission prompts:

- `Bash(php artisan:*)` — all artisan commands (migrate, make, test, scribe, queue, etc.)
- `Bash(./vendor/bin/pest:*)` and `Bash(./api-project/vendor/bin/pest:*)`
- `Bash(composer install:*)`, `Bash(composer pint:*)`, `Bash(composer require:*)`
- `Bash(npm run:*)`, `Bash(npm install:*)`, `Bash(npx:*)`
- Read/Glob/Grep wildcards (read-only is always safe)
- **Not allowlisted on purpose:** `git commit`, `git push`, `php artisan db:wipe`, `rm -rf` — destructive or shared-state operations stay manual.

Run `/fewer-permission-prompts` periodically to refine the allowlist based on actual usage.

### 9.3 MCP servers

**Default stance: no MCP servers.** They bloat context on every turn and we want lean sessions.

**The one exception: Laravel Boost** — already installed, kept because Laravel-aware introspection (DB schema, logs, error traces, Eloquent helpers) is high-value enough to justify the token cost for this stack.

For everything else, prefer CLI tools over MCP:
- GitHub: `gh` CLI, not GitHub MCP
- Filesystem: built-in Read/Glob/Grep, not filesystem MCP
- Database: `php artisan tinker` or Boost, not a database MCP

### 9.4 Custom skills (`.claude/skills/`)

Skills we'll create as the project grows:

- `/sprint-start` — opens current sprint's plan, branches off main, reminds of DoD, creates initial todos.
- `/sprint-finish` — runs PHPUnit, Pint, Scribe regen; verifies DoD checklist; opens PR draft.
- `/laravel-endpoint` — scaffolds the standard 5 files (Controller, FormRequest, Resource, Policy, Action) for a new endpoint.
- `/bruno-update` — regenerates Bruno collection entry for a given route.
- `/scribe` — runs `php artisan scribe:generate` and shows the diff.

Skills come *as we hit the repetition* — don't pre-build them. The trigger is "I've done this manually 3+ times."

### 9.5 Subagents

Used for tasks that read many files or need isolation from main context:

- **codebase-analyzer / Explore** — broad investigation when planning a sprint ("find every place we serialize a User").
- **superpowers:code-reviewer** — after each sprint, reviews against the design spec and DoD before merge.
- **security-reviewer** (created when payments sprint starts) — Stripe integration review, auth review.
- **Parallel dispatch pattern** — when 2+ independent investigations are needed (e.g., "find existing auth patterns" + "find existing notification patterns" before Sprint 5).

### 9.6 Hooks

Hooks are non-negotiable enforcement. Use sparingly; over-hooking is friction.

Recommended for this project:

- **PreToolUse on Edit, blocks edits to existing migration files** — forces "create new migration" instead of "edit migration."
- **PreToolUse on Bash, blocks `git commit`/`git push`** — already exists per user preference.
- **PostToolUse on Edit (api-project/app/**)**: optional, runs `composer pint` on the changed file.
- **Stop hook** — verifies tests pass before Claude declares a sprint complete.

Hooks live in `.claude/settings.json`. Add them as patterns surface, not preemptively.

### 9.7 Workflow loop (per sprint)

The mandated cycle for every sprint, derived from the docs' "Explore → Plan → Implement → Commit" pattern:

1. **Brainstorm sprint scope** (`superpowers:brainstorming`) — adjusts the design if needed.
2. **Write sprint plan** (`superpowers:writing-plans`) — concrete steps, files, tests.
3. **Plan Mode for tricky exploration** — read existing code without editing.
4. **Execute plan** (`superpowers:executing-plans`) — TDD per task, checkpoints between tasks.
5. **Verify before completion** (`superpowers:verification-before-completion`) — run all DoD checks.
6. **Code review** (`superpowers:requesting-code-review`) — self-review before PR.
7. **User commits + merges** — manual, per user preference.

### 9.8 Context discipline

Per the docs: "Claude's context window fills up fast, and performance degrades as it fills."

- `/clear` between sprints (always).
- `/compact` mid-sprint if context gets long.
- Subagents for investigation so exploration doesn't pollute the main session.
- Reference files via `@` syntax instead of paste-quoting them.
- `/rewind` to undo wrong directions instead of arguing.

### 9.9 Dos and Don'ts

**Do:**
- Provide verification criteria upfront (tests, expected outputs).
- Scope investigations narrowly; use subagents for breadth.
- Always Plan Mode → Implementation for multi-file changes.
- Push to git often — checkpoints aren't long-term state.

**Don't:**
- Mix unrelated tasks in one session.
- Repeat-correct the same mistake (after 2 corrections, `/clear` and re-prompt).
- Over-specify `CLAUDE.md` (rules get drowned).
- Trust generated code without running tests.

---

## 10. Implementation Plan Strategy

This design covers all 10 sprints, but a single implementation plan would be unmanageable. The strategy is: **one implementation plan per sprint**, written when that sprint is ready to start. The brainstorming → design → plan flow runs per-sprint, with this doc as the persistent reference for architecture, conventions, and DoD.

Sprint 0 will be the first plan written (next step after this design is approved).

---

## 11. Open Questions (Defer to Implementation)

- Exact role hierarchy (does agency manage multiple brands?)
- Whether `agency` has its own portal or just elevated brand permissions
- Multi-tenancy strategy for country admin (row-scoped vs schema-scoped) — likely row-scoped
- Push notification provider for Sprint 5 (FCM is free but requires account setup — stub for now is fine)

These get resolved in the implementation plan or per-sprint as needed.

---

## 12. Out of Scope (Future Designs)

- AI Phase 2A: Health Score, Brand Safety Scanner, Matching Engine, Performance Prediction, Sales Intelligence
- AI Phase 2B: AI Agent (both sides), Trend Scanner, Mood Board compliance, Amplification Network, AI Video Reports, Paid Ads Boosting, AI Draft Reviewer, AI Chatbot
- FastAPI service architecture
- AWS infrastructure (Bedrock, Rekognition, Transcribe, SageMaker, ECS Fargate, CDK)
- pgvector / RAG foundations
- iOS / Android React Native apps
- Production deployment

Each will get its own design doc when its phase begins.
