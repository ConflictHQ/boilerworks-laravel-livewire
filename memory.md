# Boilerworks Memory

This file is the **AI context seed** for the Boilerworks Laravel + Livewire template. It captures decisions, constraints, and non-obvious facts that are not derivable from reading a single file.

For conventions and patterns, see [`bootstrap.md`](bootstrap.md) (currently a stub — the working conventions live in `CLAUDE.md` until the fleet-audit consolidation lands).

---

## Template purpose

Server-rendered reactive PHP starter: Laravel 12, Livewire 3 full-page components alongside classic controllers, Filament v3 admin, Tailwind + Alpine. Ships with session auth, Spatie group-based permissions, Items/Categories CRUD, a JSON-schema forms engine, and a JSON-defined workflow (state machine) engine.

## Key architectural decisions

| Decision | Why |
|---|---|
| Livewire over a JS framework | SPA-like interactivity from Blade; the same domain is exposed twice — controller routes (`/items`) and Livewire full-page components (`/lw/items`) — as reference implementations |
| UUID PKs via `HasUuid` trait | Route key is `uuid`, never the integer id |
| Audit trails via `HasAuditTrail` | `created_by` / `updated_by` set automatically |
| Soft deletes on all domain models | Never hard-delete business data |
| Permission middleware at route level | Laravel 12 removed controller `$this->middleware()`; guards live in the route files |
| Feature toggles in `config/features.php` | `FEATURE_FORMS`, `FEATURE_WORKFLOWS`, `FEATURE_SEARCH` env-driven; seeder branches on them |

## Things that bite newcomers

- **Tests never touch Postgres** — Pest uses SQLite in-memory, plain `auth` middleware (not `auth:sanctum`), and seeds permissions via `PermissionSeeder` in `Pest.php`. `php artisan test` needs no running services.
- **Seed credentials** are `admin@` / `editor@` / `viewer@boilerworks.dev`, all password `password` (`database/seeders/DatabaseSeeder.php`). Dev-only; change before any real deployment.
- **`FEATURE_HORIZON` is a dead flag** — defined in `config/features.php` and set in `.env.example`/compose, but `laravel/horizon` is not installed and nothing reads it. Known P2; see the fleet-audit issue (#46).
- **composer.lock is stale (as of 2026-07)** — `composer audit` fails CI on main with 27 advisories; needs a `composer update` within existing constraints. See fleet-audit issue (#46).
- **App serves on 8000** — nginx in `docker/docker-compose.yml` publishes 8000; `.env.example` `APP_URL` matches.

## Release status

Template is feature-complete (8 models, forms + workflow engines, Filament admin, Pest suite, CI, docker). CI Security Audit job is red pending the dependency refresh; Lint/Tests/Build are green.
