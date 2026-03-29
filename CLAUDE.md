# Claude -- Boilerworks Laravel + Livewire

Primary conventions doc: [`bootstrap.md`](bootstrap.md)

Read it before writing any code.

## Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Livewire 3 + Blade + Tailwind CSS + Alpine.js
- **API**: Livewire (server-rendered reactive components)
- **ORM**: Eloquent (UUID PKs, soft deletes, audit trails)
- **Jobs**: Laravel Queues + Redis
- **Auth**: Laravel session-based (built-in)
- **Permissions**: Spatie Laravel Permission v6 (group-based roles)
- **Admin**: Filament v3
- **Testing**: Pest PHP
- **Database**: PostgreSQL 16
- **Cache/Broker**: Redis 7

## Key Patterns

- UUID PKs via `HasUuid` trait (route key = `uuid`)
- Audit trails via `HasAuditTrail` trait (`created_by`/`updated_by`)
- Soft deletes on all domain models
- Permission middleware applied at route level (not controller constructors -- L12 removed `$this->middleware()`)
- Feature toggles via `config/features.php` + env vars
- Forms engine: JSON Schema definitions, dynamic field rendering
- Workflow engine: state machine with transition logging

## Ports

- App: 8000
- PostgreSQL: 5432
- Redis: 6379

## Testing

```bash
php artisan test
```

Tests use SQLite in-memory, `auth` middleware (not `auth:sanctum`), and seed permissions via `PermissionSeeder` in `Pest.php`.

## Docker

```bash
cd docker && docker compose up -d --build
```
