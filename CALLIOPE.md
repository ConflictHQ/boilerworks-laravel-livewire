# Calliope — Boilerworks Laravel + Livewire
<!-- Agent shim for https://github.com/calliopeai/calliope-cli -->

Primary conventions doc: [`bootstrap.md`](bootstrap.md)
Context seed: [`memory.md`](memory.md)

Read both before writing any code.

---

## Project-specific notes

- Laravel 12 (PHP 8.2+) + Livewire 3 + Blade + Tailwind + Alpine.js; Filament v3 admin; Postgres 16 + Redis 7. Template is still under development — bootstrap.md is minimal, defer to the stack primer.
- Eloquent models use `HasUuid` (route key = `uuid`) and `HasAuditTrail` (`created_by`/`updated_by`) traits; soft deletes on all domain models; never expose integer IDs.
- Session-based auth (built-in); Spatie Laravel Permission v6, group-based roles; permission middleware applied at route level (L12 removed controller-constructor `$this->middleware()`).
- Forms engine (JSON Schema definitions, dynamic field rendering) and workflow engine (state machine + transition logging); feature toggles via `config/features.php` + env vars.
- Tests use Pest PHP, SQLite in-memory, `auth` middleware (not `auth:sanctum`), permissions seeded via `PermissionSeeder`; run `php artisan test`.
- Docker: `cd docker && docker compose up -d --build`; app :8000.
