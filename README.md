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

## Docker (Laravel Sail)

Prefer containers? This project ships a **sqlite-only Laravel Sail** setup where every common operation runs inside the container. The host workflow above stays the default; Sail is an equal alternative, not an afterthought.

> **How this differs from stock Sail.** A default `sail:install` brings up php-fpm plus MySQL/Redis/mailpit and friends. This project overrides two things (see [ADR #0004](docs/adr/0004-adopt-laravel-sail.md)):
>
> - The app container runs this project's **`composer dev`** orchestration (serve + queue + pail + Vite) instead of php-fpm, so `sail up` starts the same stack as the host.
> - There is **no database service** — the project is sqlite-only, and the sqlite file plus `storage/logs` persist on the host via the bind mount. A one-shot `node-setup` service bootstraps a Linux-native `node_modules` volume so the host and container don't fight over platform-specific native binaries.

### Bootstrap

The Sail CLI lives at `vendor/bin/sail`, so two steps run on the host first to avoid a container↔env circular dependency:

```bash
composer install                      # on the host — provides vendor/bin/sail
cp .env.example .env
php artisan key:generate             # on the host — the container reads .env, so it must exist first
./vendor/bin/sail up -d              # builds the image, bootstraps node_modules, runs composer dev
./vendor/bin/sail artisan migrate
```

Tip — alias it once and use the bare `sail` form everywhere below:

```bash
alias sail='./vendor/bin/sail'
```

### Cheat-sheet (Sail-only)

The host commands above map directly onto Sail's canonical names:

| You want to…              | Sail command                          |
|---------------------------|---------------------------------------|
| Start everything          | `sail up` / `sail up -d`              |
| Stop                      | `sail stop`                           |
| Run the test suite        | `sail test` (or `sail artisan test`)  |
| Run migrations            | `sail artisan migrate`                |
| Fresh + seed the database | `sail artisan migrate:fresh --seed`   |
| Drop into tinker          | `sail tinker`                         |
| Run any Artisan command   | `sail artisan …`                      |
| Run an npm script         | `sail npm …`                          |

Notes:

- `sail up` runs the **full `composer dev` stack** (serve + queue + pail + Vite) — there is no separate `sail dev`.
- Use **`sail stop`**, not `sail down` — `stop` keeps the container around for a fast next `sail up`, and your sqlite file and logs persist on the host regardless.
- Use **`sail artisan migrate:fresh --seed`** — Sail has no `sail fresh` alias.

For anything beyond this cheat-sheet (xdebug, `sail share`, database CLIs, the full command surface), see the [official Laravel Sail documentation](https://laravel.com/docs/sail).

## Project Structure

Standard Laravel 13 layout. Key additions:

```
.pi/                 — Pi config: settings, skills, prompts
AGENTS.md            — Project instructions (Boost + agent workflow)
docs/agents/         — Agent documentation (domain, issues, triage)
CONTEXT.md           — Domain context for AI agents
```

Architectural decisions live in `docs/adr/`.

## Authentication

Laradmin ships with three independent login methods. Each can be enabled or disabled via environment variables.

### Password Login

Enabled by default. Uses Laravel Breeze (Blade stack).

```env
AUTH_PASSWORD_LOGIN=true
```

When disabled, the email/password form and registration page are hidden entirely.

### Google Login

Uses Laravel Socialite with Google OAuth 2.0.

**1. Create Google OAuth credentials**

Go to [Google Cloud Console](https://console.cloud.google.com/apis/credentials), create a project, enable the Google+ API, and create OAuth 2.0 Client ID credentials.

Set the **Authorized redirect URI** to:
```
https://your-domain.com/login/google/callback
```

**2. Configure environment**

```env
AUTH_GOOGLE=true
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
```

### Generic OIDC Login

Uses `kovah/laravel-socialite-oidc` to support any OpenID Connect-compliant identity provider (Authentik, Keycloak, etc.).

**1. Register an OIDC client**

In your identity provider's admin console, create a new OIDC client with:

- **Redirect URI:** `https://your-domain.com/login/oidc/callback`
- **Scopes:** `openid profile email`

**2. Configure environment**

```env
AUTH_OIDC=true
OIDC_CLIENT_ID=your-oidc-client-id
OIDC_CLIENT_SECRET=your-oidc-client-secret
OIDC_BASE_URL=https://your-idp.example.com/application/o/your-app/
OIDC_REDIRECT_URI=https://your-domain.com/login/oidc/callback
```

> **Note:** `OIDC_BASE_URL` should point to your provider's application root (without `.well-known/openid-configuration` — the package appends that automatically).

### Auth Toggles

All three methods can be toggled independently:

```env
AUTH_PASSWORD_LOGIN=true   # Email/password form + registration
AUTH_GOOGLE=true           # Google OAuth button + routes
AUTH_OIDC=true             # OIDC button + routes
```

Any combination is valid. If all three are `false`, the login page shows a "Login is disabled" message.

## License

[MIT](https://opensource.org/licenses/MIT)
