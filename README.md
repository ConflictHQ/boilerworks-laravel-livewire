# Boilerworks Laravel + Livewire

Server-rendered reactive PHP with Laravel 12 and Livewire 3. Livewire handles dynamic components server-side, so you get SPA-like interactivity with Blade templates and zero JavaScript framework overhead. Choose this over Laravel + Vue when your team is PHP-first and the UI complexity doesn't justify a separate JS frontend.

## Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12 (PHP 8.2+) |
| Frontend | Livewire 3 + Blade + Tailwind CSS + Alpine.js |
| Admin | Filament v3 |
| Database | PostgreSQL 16 |
| Cache/Broker | Redis 7 |
| Jobs | Laravel Queues + Redis |
| Auth | Laravel session-based (built-in) |
| Permissions | Spatie Laravel Permission v6 (group-based roles) |
| Testing | Pest PHP |
| Linter | Laravel Pint |

## Quick Start

### Docker

```bash
# Start the stack (nginx on http://localhost:8000)
./run.sh up

# Seed the database
./run.sh seed

# Run tests / linter
./run.sh test
./run.sh lint
```

### Local (without Docker)

```bash
# Install dependencies, create .env, generate key, migrate, build assets
composer setup

# Run the dev stack (server + queue + logs + vite)
composer dev
```

**Seeded users** (all password `password`, dev-only):
- `admin@boilerworks.dev` (admin role, full access)
- `editor@boilerworks.dev` (editor role)
- `viewer@boilerworks.dev` (viewer role, view-only)

## What's Included

- **Items/Categories CRUD** — both classic controller routes and Livewire full-page components (`/lw/items`, `/lw/categories`)
- **Forms engine** — JSON Schema form definitions with dynamic field rendering and submissions
- **Workflow engine** — state machine with conditional transitions and transition logging
- **Filament admin panel** at `/admin`
- **UUID primary keys** (`HasUuid` trait, route key = `uuid`), **audit trails** (`HasAuditTrail`), soft deletes on all domain models
- **Feature toggles** via `config/features.php` + env vars
- **Health check** at `/status` (JSON)

## Ports

| Service | Port |
|---------|------|
| App (nginx) | 8000 |
| PostgreSQL | 5432 |
| Redis | 6379 |
| MinIO | 9000 / 9001 |
| Mailpit | 8025 (UI) / 1025 (SMTP) |

## Testing

```bash
php artisan test
```

Tests use SQLite in-memory and need no running services.

## Conventions

See [`bootstrap.md`](bootstrap.md), the [Boilerworks Catalogue](https://github.com/ConflictHQ/boilerworks/blob/main/primers/CATALOGUE.md), and the [stack primer](https://github.com/ConflictHQ/boilerworks/blob/main/primers/laravel-livewire/PRIMER.md) for architecture and conventions. Contributions welcome — see [CONTRIBUTING.md](CONTRIBUTING.md).

---

Boilerworks is a [CONFLICT](https://weareconflict.com) brand. CONFLICT is a registered trademark of CONFLICT LLC.
