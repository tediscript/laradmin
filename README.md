# Laradmin — Laravel Admin Starter Kit

Reusable starter kit for building admin-heavy web applications. Fork this repo to spin up a new project quickly.

## Stack

| Layer         | Technology                                          |
|---------------|-----------------------------------------------------|
| Framework     | Laravel 13                                          |
| PHP           | 8.3+                                                |
| Frontend      | Blade + Alpine.js + Tailwind CSS                    |
| Asset bundler | Vite                                                |
| Auth          | Laravel Breeze (Blade stack)                        |
| Database      | SQLite (default, swap to MySQL/Postgres as needed)  |
| Testing       | Pest 4.x                                            |
| API           | `routes/api.php` ready (add Sanctum for token auth) |

## Setup

```bash
make setup
```

Installs dependencies, creates `.env`, generates key, migrates, and builds assets.

Or step by step:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

## Run

```bash
make dev
```

Starts all services concurrently (PHP server + queue worker + log viewer + Vite HMR).

Or individually:

```bash
make serve                  # PHP dev server
php artisan queue:listen    # Queue worker
php artisan pail            # Live log viewer
npm run dev                 # Vite HMR (auto-rebuild on file change)
```

## Test

```bash
make test
```

Clears config cache, then runs the Pest test suite.

## Build

```bash
make build
```

Compiles and bundles all frontend assets via Vite for production.

## Project Structure

Standard Laravel 13 layout. Key additions:

```
.clinerules/          — Agent skills config (Cline AI)
docs/agents/          — Agent documentation (domain, issues, triage)
CONTEXT.md            — Domain context for AI agents
```

Architectural decisions live in `docs/adr/`.

## License

[MIT](https://opensource.org/licenses/MIT)