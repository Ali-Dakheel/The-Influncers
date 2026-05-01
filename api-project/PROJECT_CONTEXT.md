# Project Context — Backend Env Structure

This file documents the structure of `.env` (which is not read by Claude). Update it whenever env keys change.

## Database (Sprint 0)
- `DB_CONNECTION=pgsql`
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

## Bruno API client

Collection at `bruno/influencer-system/`. To use it:
1. Install Bruno: https://www.usebruno.com/downloads
2. Open Bruno → Open Collection → select `api-project/bruno/influencer-system`
3. Create a local environment with `baseUrl=http://localhost:8000`
4. Run requests against the local Laravel server (`php artisan serve`)

Environments are gitignored.
