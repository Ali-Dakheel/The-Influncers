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
