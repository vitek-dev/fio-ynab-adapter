# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

- Install deps: `composer install`
- Run the adapter (Fio → YNAB sync): `php bin/run.php`
- Manually rewind Fio's "last downloaded" pointer: `php bin/reset-fio.php <transactionId>`
- Run via Docker Compose (loads env from `.env`): `docker compose run --rm app php bin/run.php`

There is no test suite, linter, or build step configured.

## Architecture

The app is a one-shot CLI sync: `bin/run.php` boots `App` (a hand-rolled DI container), then calls `AdapterService::run()` which pipes Fio transactions through resolvers into YNAB.

**Three-layer pipeline** (`src/AdapterService.php`):
1. `SourceRepository::fetchTransactions()` — pulls new Fio transactions since the last-downloaded pointer (Fio API tracks this server-side; `bin/reset-fio.php` rewinds it).
2. Each `SourceTransaction` is mapped to a `TargetTransaction` via `TargetTransaction::fromSource()`, then every registered `TransactionResolver` mutates the target in turn. Final step: title-cases `payeeName`.
3. `TargetRepository::pushTransactions()` posts the batch to YNAB. `import_id` is set to Fio's `transactionId`, which means **YNAB will silently reject re-imports of deleted transactions** (see README troubleshooting).

**Resolver chain** (`src/Resolver/`): order matters and is fixed in `App::__construct`. `BankTransfer` runs first to populate payee from counterparty info for transfer-type transactions. `CardPayment` runs next and is the catch-all `Nákup:` parser — it must stay above the more specific card-payment resolvers (`GoPay`, `ComGate`) per the inline comment, because each more-specific resolver overrides the payee name set by `CardPayment`. To support a new payment processor, add a resolver matching its `userIdentification` prefix and register it in `App::__construct` *after* `CardPayment`.

**DI container** (`src/App.php`): services are stored as arrays keyed by both interface and concrete class, so `getService(SourceRepository::class)` and `getService(FioSourceRepository::class)` resolve the same instance. `getService` throws if a key has 0 or >1 entries; `getServices` returns the list (used for the resolver chain).

## Conventions

- PHP 8.5, `declare(strict_types=1)` everywhere, `final readonly` classes, `#[\Override]` and `#[\NoDiscard]` attributes are used consistently.
- The pipe operator `|>` (PHP 8.5) is used in `AdapterService::run()` — preserve this style for similar pipelines.
- HTTP is done via `file_get_contents` + `stream_context_create` (no Guzzle/cURL dep). Keep it that way unless adding a real dependency is justified.
- Config is environment-only: `config/env.php` defines constants from `getenv()` and throws if any are missing. `.env` is loaded by docker-compose, not by PHP — running `php bin/run.php` directly requires the env vars to already be exported in the shell.
