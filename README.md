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
