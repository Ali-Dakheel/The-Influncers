# Sprint 0 — Foundation & Tooling Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Set up the Claude Code workflow + Laravel scaffolding baseline so every subsequent sprint can run cleanly.

**Architecture:** Traditional Laravel with Action pattern (per design spec §4). PHPUnit for tests. PostgreSQL local DB. Per-project Claude Code config (`/.claude/settings.json` at repo root) plus monorepo CLAUDE.md hierarchy (root + `api-project/`).

**Tech Stack:** Laravel 13 (PHP 8.4), PHPUnit 12, Laravel Pint, Scribe (API docs), Laravel Boost (already installed), PostgreSQL local, Bruno (file-based API client).

**Reference:** [Design spec](../specs/2026-05-01-influencer-system-laravel-design.md) §6 (Sprint 0) and §9 (Claude Code workflow).

**User-side rules in effect** (from memory):
- No `git commit` / `git push` from Claude — user commits manually
- No reading `.env` / `.env.*` files — describe edits as instructions
- No new MCP servers (Laravel Boost already installed is the one exception)

---

## File Structure

**Files this sprint will create:**

| Path | Purpose |
|---|---|
| `CLAUDE.md` (repo root) | Monorepo overview, points to subfolder CLAUDE.md, references design spec |
| `.claude/settings.json` (repo root) | Permissions allowlist + migration-protect hook |
| `api-project/PROJECT_CONTEXT.md` | Documents env structure (since `.env` can't be read) |
| `api-project/app/Actions/Demo/PingAction.php` | First Action class (sets the pattern) |
| `api-project/app/Http/Controllers/Demo/PingController.php` | Thin controller that invokes the Action |
| `api-project/tests/Feature/Demo/PingTest.php` | PHPUnit feature test (TDD) |
| `api-project/bruno/influencer-system/bruno.json` | Bruno collection metadata |
| `api-project/bruno/influencer-system/Demo/Ping.bru` | First Bruno request |
| `api-project/bruno/influencer-system/.gitignore` | Bruno secrets ignore |

**Files this sprint will modify:**

| Path | Change |
|---|---|
| `api-project/.env` | DB driver `sqlite` → `pgsql` (user applies; Claude provides instructions) |
| `api-project/.env.example` | Same change, committed |
| `api-project/routes/api.php` | Add `GET /ping` route |
| `api-project/CLAUDE.md` | Append "Project Conventions" section (existing Boost section preserved) |
| `.gitignore` (repo root) | Add `bruno/**/environments/` if not already covered |

---

## Task 1: Update `api-project/.env.example` and local `.env` to PostgreSQL

**Files:**
- Modify (by user): `api-project/.env.example`
- Modify (by user): `api-project/.env`

Claude cannot read or edit `.env*` files (hook blocks all of them). Claude prints exact instructions; the user applies them.

- [ ] **Step 1: User applies the env edits**

Print this instruction block to the user verbatim:

> Open `api-project/.env.example` and `api-project/.env`. In **both files**, replace the database block with exactly:
>
> ```
> DB_CONNECTION=pgsql
> DB_HOST=127.0.0.1
> DB_PORT=5432
> DB_DATABASE=influencer_system
> DB_USERNAME=postgres
> DB_PASSWORD=postgres
> ```
>
> Then ensure PostgreSQL is running locally and a database named `influencer_system` exists. Quick way to create it:
>
> ```bash
> psql -U postgres -c "CREATE DATABASE influencer_system;"
> ```
>
> Reply "done" when both files are updated and the database exists.

Wait for the user to confirm before moving to Step 2.

- [ ] **Step 2: After user confirms, document the env structure in `api-project/PROJECT_CONTEXT.md`**

Use Write to create `api-project/PROJECT_CONTEXT.md` with:

```markdown
# Project Context — Backend Env Structure

This file documents the structure of `.env` (which is not read by Claude). Update it whenever env keys change.

## Database
- `DB_CONNECTION=pgsql` (Sprint 0 onwards)
- `DB_HOST=127.0.0.1`
- `DB_PORT=5432`
- `DB_DATABASE=influencer_system`
- `DB_USERNAME=postgres`
- `DB_PASSWORD=postgres`

## Mail (Sprint 0)
- `MAIL_MAILER=log` — writes to `storage/logs/laravel.log`

## Queue (introduced Sprint 5)
- `QUEUE_CONNECTION=database` — DB-driver queue, no Redis

## Filesystem (Sprint 0)
- `FILESYSTEM_DISK=public` — local public disk

## Stripe (Sprint 7 onwards)
- `STRIPE_KEY=pk_test_...`
- `STRIPE_SECRET=sk_test_...`
- `STRIPE_WEBHOOK_SECRET=whsec_...`
```

- [ ] **Step 3: Stage; user commits manually**

```bash
git add api-project/.env.example api-project/PROJECT_CONTEXT.md
```

User commits when ready. (`.env` is gitignored and stays local.)

---

## Task 1.5: Verify Laravel Boost is installed

**Files:** none modified — verification only.

User stated Boost was installed earlier. Confirm.

- [ ] **Step 1: Check composer.json**

Use Read on `api-project/composer.json` and confirm `"laravel/boost"` appears in `require-dev` (it does in the kit baseline — version `^2.4`). If missing, run from `api-project/`:

```bash
composer require laravel/boost --dev
```

- [ ] **Step 2: Confirm boost.json exists**

```bash
test -f api-project/boost.json && echo OK || echo MISSING
```

Expected: `OK`. If `MISSING`, run from `api-project/`:

```bash
php artisan boost:install --no-interaction
```

This generates `boost.json` and updates `api-project/CLAUDE.md` with Boost guidelines (already present per the existing CLAUDE.md). No further action needed; we don't need to enable the MCP transport — Boost works as a Composer package without it.

---

## Task 2: Verify Postgres connection

**Files:** none modified — verification only.

- [ ] **Step 1: Run a tinker check from `api-project/`**

```bash
php artisan tinker --execute 'DB::connection()->getPdo();'
```

Expected: no exception, returns a PDO instance representation. If it fails with "could not find driver", the user needs to enable `pdo_pgsql` in `php.ini`. If it fails with "could not connect to server", Postgres isn't running or credentials are wrong — stop and ask the user.

- [ ] **Step 2: Run migrate:fresh against Postgres**

```bash
php artisan migrate:fresh
```

Expected: all migrations run, ends with "Migration table created successfully" + each migration name "DONE".

- [ ] **Step 3: Run the existing test suite**

```bash
php artisan test --compact
```

Expected: all existing tests pass (HealthTest + Auth tests). If any fail because of test DB config, check `phpunit.xml` for `DB_CONNECTION` env override — it currently overrides to in-memory; that's fine and expected.

---

## Task 3: Add Action pattern with TDD `/ping` example

**Files:**
- Create: `api-project/tests/Feature/Demo/PingTest.php`
- Create: `api-project/app/Actions/Demo/PingAction.php`
- Create: `api-project/app/Http/Controllers/Demo/PingController.php`
- Modify: `api-project/routes/api.php`

This sets the Action pattern that every subsequent sprint follows.

- [ ] **Step 1: Write the failing test**

Use Write to create `api-project/tests/Feature/Demo/PingTest.php`:

```php
<?php

namespace Tests\Feature\Demo;

use Tests\TestCase;

class PingTest extends TestCase
{
    public function test_ping_endpoint_returns_pong(): void
    {
        $response = $this->getJson('/api/ping');

        $response->assertOk()
            ->assertExactJson(['message' => 'pong']);
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

```bash
php artisan test --compact --filter=PingTest
```

Expected: 1 failed. The error should be a 404 — route doesn't exist.

- [ ] **Step 3: Create the Action class**

Use Write to create `api-project/app/Actions/Demo/PingAction.php`:

```php
<?php

namespace App\Actions\Demo;

class PingAction
{
    public function __invoke(): array
    {
        return ['message' => 'pong'];
    }
}
```

- [ ] **Step 4: Create the Controller**

Use Write to create `api-project/app/Http/Controllers/Demo/PingController.php`:

```php
<?php

namespace App\Http\Controllers\Demo;

use App\Actions\Demo\PingAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PingController extends Controller
{
    public function __invoke(PingAction $action): JsonResponse
    {
        return response()->json($action());
    }
}
```

- [ ] **Step 5: Register the route**

Use Edit on `api-project/routes/api.php` to add at the end of the file (after the closing `});` of any existing groups, but before the closing `?>` if present):

```php
use App\Http\Controllers\Demo\PingController;

Route::get('ping', PingController::class)->name('ping');
```

If a `use App\Http\Controllers\Demo\PingController;` doesn't fit at the top with other use statements, group it logically with the existing controller imports.

- [ ] **Step 6: Run the test to verify it passes**

```bash
php artisan test --compact --filter=PingTest
```

Expected: 1 passed.

- [ ] **Step 7: Run Pint on changed files**

```bash
vendor/bin/pint --dirty --format agent
```

Expected: changes formatted; exits with success.

- [ ] **Step 8: Stage; user commits manually**

```bash
git add api-project/app/Actions api-project/app/Http/Controllers/Demo api-project/tests/Feature/Demo api-project/routes/api.php
```

User commits when ready.

---

## Task 4: Create root `CLAUDE.md`

**Files:**
- Create: `CLAUDE.md` (repo root)

- [ ] **Step 1: Write the file**

Use Write to create `CLAUDE.md` (at `C:\Users\User\Desktop\The Influncers\CLAUDE.md`) with:

```markdown
# The Influencer System — Repo Overview

AI-native influencer marketing platform. Monorepo: Laravel API backend + Next.js frontend.

## Layout

- [`api-project/`](api-project/) — Laravel 13 (PHP 8.4) JSON API. See [`api-project/CLAUDE.md`](api-project/CLAUDE.md) for backend-specific rules.
- [`frontend/`](frontend/) — Next.js. CLAUDE.md added in Sprint 10.
- [`docs/superpowers/`](docs/superpowers/) — Specs and per-sprint plans.

## Active design

- [Backend design spec](docs/superpowers/specs/2026-05-01-influencer-system-laravel-design.md) — read before any backend work.

## Universal rules

- **Never run `git commit` or `git push`.** User commits manually. (Hook enforces.)
- **Never read `.env` or `.env.*`.** Refer to `api-project/PROJECT_CONTEXT.md` for env structure.
- **No new MCP servers.** Laravel Boost is the only exception.
- **Backend-first workflow.** Build the Laravel API completely (sprints 0–9), then the Next.js frontend (sprint 10). Test the API with Bruno collections at `api-project/bruno/`.

## Workflow per sprint

Use the superpowers skills in this order: `brainstorming` (if scope is unclear) → `writing-plans` → `subagent-driven-development` or `executing-plans`. Plans live in `docs/superpowers/plans/`.

## Branch & commit style

- One sprint per feature branch: `sprint-N-name` (e.g. `sprint-1-identity`).
- Commits: short, imperative, present-tense ("add UserPolicy").
```

- [ ] **Step 2: Stage; user commits manually**

```bash
git add CLAUDE.md
```

User commits when ready.

---

## Task 5: Append project conventions to `api-project/CLAUDE.md`

**Files:**
- Modify: `api-project/CLAUDE.md`

The file already has Laravel Boost-generated guidelines (lines 1–146). We append our project-specific section without touching the Boost block.

- [ ] **Step 1: Append the project conventions section**

Use Edit on `api-project/CLAUDE.md` to add after the closing `</laravel-boost-guidelines>` tag (line 147 is the file end):

```markdown

---

## Project Conventions (Influencer System)

These supplement the Laravel Boost guidelines above with project-specific rules from the [design spec](../docs/superpowers/specs/2026-05-01-influencer-system-laravel-design.md).

### Action Pattern

Every business operation lives in an invokable `Action` class under `app/Actions/<Domain>/`. Controllers stay thin: validate via FormRequest, authorize via Policy, dispatch to one Action.

Example: `app/Actions/Demo/PingAction.php` — the canonical pattern.

### Per-endpoint files (the "5-file rule")

Every write endpoint creates these files:
1. **FormRequest** — validation (`app/Http/Requests/<Domain>/<Verb><Noun>Request.php`)
2. **Action** — single-purpose business operation (`app/Actions/<Domain>/<Verb><Noun>Action.php`)
3. **Controller** — invokable, thin (`app/Http/Controllers/<Domain>/<Verb><Noun>Controller.php`)
4. **Resource** — response shape (`app/Http/Resources/<Noun>Resource.php`)
5. **Policy** — authorization (`app/Policies/<Noun>Policy.php`)

Read endpoints can skip FormRequest if they have no input.

### Migrations

- **Never edit existing migrations.** Always `php artisan make:migration` for changes. (Enforced by hook in `.claude/settings.json`.)
- Migrations must be reversible (`down()` implemented).
- After every model migration, also create a factory and seeder.

### State transitions

Every state transition fires an Event. Side effects (notifications, audit logs) live in Listeners, not in the Action.

### Outcome records

Every campaign completion writes an `OutcomeRecord` (created Sprint 4). Even though AI features that read it ship in Phase 2A, the data must accumulate from day one.

### Per-sprint Definition of Done

- All PHPUnit tests pass: `php artisan test --compact`
- Pint passes: `vendor/bin/pint --test`
- Scribe regenerated: `php artisan scribe:generate` (output at `/docs`)
- Bruno collection updated for new endpoints (at `bruno/influencer-system/`)
- Migrations reversible
- `php artisan migrate:fresh --seed` produces a usable demo dataset
```

- [ ] **Step 2: Stage; user commits manually**

```bash
git add api-project/CLAUDE.md
```

User commits when ready.

---

## Task 6: Create `.claude/settings.json` at repo root

**Files:**
- Create: `.claude/settings.json` (repo root)

- [ ] **Step 1: Write the settings file**

Use Write to create `.claude/settings.json` (at `C:\Users\User\Desktop\The Influncers\.claude\settings.json`) with:

```json
{
  "permissions": {
    "allow": [
      "Read(*)",
      "Glob(*)",
      "Grep(*)",
      "Bash(php artisan:*)",
      "Bash(php artisan test:*)",
      "Bash(php artisan migrate:*)",
      "Bash(php artisan tinker:*)",
      "Bash(php artisan scribe:*)",
      "Bash(php artisan queue:*)",
      "Bash(php artisan make:*)",
      "Bash(php artisan route:*)",
      "Bash(php artisan config:*)",
      "Bash(vendor/bin/pint:*)",
      "Bash(composer install:*)",
      "Bash(composer require:*)",
      "Bash(composer update:*)",
      "Bash(composer pint:*)",
      "Bash(npm run:*)",
      "Bash(npm install:*)",
      "Bash(npm test:*)",
      "Bash(npx:*)",
      "Bash(git status:*)",
      "Bash(git diff:*)",
      "Bash(git log:*)",
      "Bash(git add:*)",
      "Bash(git branch:*)",
      "Bash(git checkout:*)"
    ],
    "deny": [
      "Bash(git commit:*)",
      "Bash(git push:*)",
      "Bash(git reset --hard:*)",
      "Bash(php artisan db:wipe:*)",
      "Bash(rm -rf:*)"
    ]
  },
  "hooks": {
    "PreToolUse": [
      {
        "matcher": "Edit",
        "hooks": [
          {
            "type": "command",
            "command": "grep -qE 'database/migrations/[0-9_]+_.+\\.php$' && printf '%s' '{\"hookSpecificOutput\":{\"hookEventName\":\"PreToolUse\",\"permissionDecision\":\"deny\",\"permissionDecisionReason\":\"Migration policy: never edit existing migration files. Create a new migration with php artisan make:migration instead.\"}}' || true"
          }
        ]
      },
      {
        "matcher": "Write",
        "hooks": [
          {
            "type": "command",
            "command": "grep -qE 'database/migrations/[0-9_]+_.+\\.php$' && printf '%s' '{\"hookSpecificOutput\":{\"hookEventName\":\"PreToolUse\",\"permissionDecision\":\"deny\",\"permissionDecisionReason\":\"Migration policy: do not overwrite existing migration files. Create a new migration with php artisan make:migration instead.\"}}' || true"
          }
        ]
      }
    ]
  }
}
```

The hook fires only on filenames matching the migration pattern (`database/migrations/<timestamp>_<name>.php`). New migration creation via `php artisan make:migration` is unaffected — it produces a fresh file with a new timestamp; the hook denies edits to *existing* timestamped files.

- [ ] **Step 2: Test the migration-protect hook**

Pick any existing migration file (e.g. `api-project/database/migrations/0001_01_01_000000_create_users_table.php`) and try to use Edit on it with a no-op change.

Expected: hook blocks with the "Migration policy" message.

If it does not block, the matcher pattern needs tweaking — check the actual path format and adjust the regex.

- [ ] **Step 3: Stage; user commits manually**

```bash
git add .claude/settings.json
```

User commits when ready.

---

## Task 7: Initialize Bruno collection

**Files:**
- Create: `api-project/bruno/influencer-system/bruno.json`
- Create: `api-project/bruno/influencer-system/Demo/Ping.bru`
- Create: `api-project/bruno/influencer-system/.gitignore`

Bruno is a file-based API client (Postman alternative). Collections live in plain text, git-friendly.

- [ ] **Step 1: Create the collection metadata**

Use Write to create `api-project/bruno/influencer-system/bruno.json`:

```json
{
  "version": "1",
  "name": "influencer-system",
  "type": "collection",
  "ignore": [
    "node_modules",
    ".git",
    "environments"
  ]
}
```

- [ ] **Step 2: Create the first request file**

Use Write to create `api-project/bruno/influencer-system/Demo/Ping.bru`:

```
meta {
  name: Ping
  type: http
  seq: 1
}

get {
  url: {{baseUrl}}/api/ping
  body: none
  auth: none
}

headers {
  Accept: application/json
}

assert {
  res.status: eq 200
  res.body.message: eq pong
}
```

- [ ] **Step 3: Create the Bruno gitignore**

Use Write to create `api-project/bruno/influencer-system/.gitignore`:

```
environments/
*.local.bru
```

The `environments/` folder holds local-only base URLs and secrets — never commit them.

- [ ] **Step 4: Document the Bruno setup briefly in PROJECT_CONTEXT.md**

Use Edit to append to `api-project/PROJECT_CONTEXT.md`:

```markdown

## Bruno API client

Collection at `bruno/influencer-system/`. To use it:
1. Install Bruno: https://www.usebruno.com/downloads
2. Open Bruno → Open Collection → select `api-project/bruno/influencer-system`
3. Create a local environment with `baseUrl=http://localhost:8000`
4. Run requests against the local Laravel server (`php artisan serve`)

Environments are gitignored.
```

- [ ] **Step 5: Stage; user commits manually**

```bash
git add api-project/bruno api-project/PROJECT_CONTEXT.md
```

User commits when ready.

---

## Task 8: Verify the full baseline

**Files:** none modified — verification only.

- [ ] **Step 1: Run the full test suite**

```bash
php artisan test --compact
```

Expected: all tests pass (existing Auth + Health + the new Ping test). If any fail, stop and investigate before merging Sprint 0.

- [ ] **Step 2: Run Pint in test mode**

```bash
vendor/bin/pint --test --format agent
```

Expected: no formatting issues. If issues are reported, run without `--test` to fix them, then re-run.

- [ ] **Step 3: Regenerate Scribe API docs**

```bash
php artisan scribe:generate
```

Expected: docs generated at `/docs`. Visit `http://localhost:8000/docs` after `php artisan serve` to confirm the new `/api/ping` endpoint appears.

- [ ] **Step 4: Verify migrate:fresh works**

```bash
php artisan migrate:fresh
```

Expected: clean schema rebuild against Postgres. No errors.

- [ ] **Step 5: Verify Bruno request works**

User-facing step: open Bruno, run the `Demo/Ping` request, confirm 200 with `{ "message": "pong" }`. Claude cannot do this — the user must verify visually.

---

## Task 9: Sprint 0 completion checklist

- [ ] All tests pass (Task 8 Step 1)
- [ ] Pint clean (Task 8 Step 2)
- [ ] Scribe shows `/api/ping` (Task 8 Step 3)
- [ ] `migrate:fresh` works against Postgres (Task 8 Step 4)
- [ ] Bruno Ping request returns pong (Task 8 Step 5)
- [ ] Hook blocks edits to existing migrations (Task 6 Step 2)
- [ ] Files staged for user to commit:
  - `api-project/.env.example`
  - `api-project/PROJECT_CONTEXT.md`
  - `api-project/CLAUDE.md`
  - `api-project/app/Actions/Demo/PingAction.php`
  - `api-project/app/Http/Controllers/Demo/PingController.php`
  - `api-project/tests/Feature/Demo/PingTest.php`
  - `api-project/routes/api.php`
  - `api-project/bruno/`
  - `CLAUDE.md` (repo root)
  - `.claude/settings.json` (repo root)

When all boxes are checked, Sprint 0 is done. Sprint 1 (Identity Foundation) starts with a fresh brainstorming → writing-plans cycle.
