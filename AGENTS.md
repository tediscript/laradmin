<laravel-boost-guidelines>
=== foundation rules ===
# Laravel Boost Guidelines
Curated by Laravel maintainers. Follow closely.
## Foundational Context
Laravel app. Packages & versions:
- php - 8.5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/breeze (BREEZE) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- alpinejs (ALPINEJS) - v3
- tailwindcss (TAILWINDCSS) - v3
## Skills Activation
Domain-specific skills in `**/skills/**`. Activate when working in that domain—don't wait.
## Conventions
- Follow existing code conventions. Check sibling files for structure/naming.
- Descriptive names: `isRegisteredForDiscounts`, not `discount()`.
- Reuse existing components before creating new ones.
## Verification Scripts
- No verification scripts/tinker when tests cover it. Tests > scripts.
## Application Structure & Architecture
- Stick to existing directory structure. No new base folders without approval.
- No dependency changes without approval.
## Frontend Bundling
- Frontend changes not showing? Suggest `npm run build`, `npm run dev`, or `composer run dev`.
## Documentation Files
- Only create docs when explicitly requested.
## Replies
- Concise. Skip obvious details.
=== boost rules ===
# Laravel Boost
## Tools
- Boost = MCP server. Prefer Boost tools over shell commands/file reads.
- `database-query` — read-only DB queries (vs raw SQL in tinker).
- `database-schema` — inspect table structure before migrations/models.
- `get-absolute-url` — resolve scheme/domain/port before sharing URLs.
- `browser-logs` — read browser logs/errors. Recent only, ignore old.
## Searching Documentation (IMPORTANT)
- **Always** `search-docs` before code changes. Returns version-specific docs.
- Pass `packages` array to scope results.
- Multiple broad topic queries: `['rate limiting', 'routing rate limiting', 'routing']`. Most relevant first.
- No package names in queries. `test resource table`, not `filament 4 test resource table`.
### Search Syntax
1. Words = auto-stemmed AND: `rate limit` → matches both.
2. `"quoted phrases"` = exact position: `"infinite scroll"` → adjacent, ordered.
3. Mix: `middleware "rate limit"`.
4. Multiple queries = OR: `queries=["authentication", "middleware"]`.
## Artisan
- Run via CLI: `php artisan route:list`. Discover: `php artisan list`. Params: `php artisan [cmd] --help`.
- Route filters: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Config via dot notation: `php artisan config:show app.name`. Or read `config/` files.
- Env vars: read `.env` directly.
## Tinker
- PHP in app context for debugging. No model creation without approval—use factories/tests. Prefer Artisan over custom tinker.
- Single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`
=== php rules ===
# PHP
- Curly braces for all control structures, even single-line.
- PHP 8 constructor promotion: `public function __construct(public GitHub $github) { }`. No empty `__construct()` unless private.
- Explicit return types + param type hints: `function isAccessible(User $user, ?string $path = null): bool`
- TitleCase Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- PHPDoc blocks > inline comments. Inline comments only for exceptionally complex logic.
- Array shape types in PHPDoc.
=== deployments rules ===
# Deployment
- Deploy via [Laravel Cloud](https://cloud.laravel.com/) — fastest option.
=== tests rules ===
# Test Enforcement
- Every change must be tested. New or updated test, then run affected tests.
- Minimal test runs: `php artisan test --compact` with filename/filter.
=== laravel/core rules ===
# Do Things the Laravel Way
- `php artisan make:` for new files. Discover: `php artisan list`. Params: `php artisan [cmd] --help`.
- Generic PHP class: `php artisan make:class`.
- `--no-interaction` on all Artisan commands. Pass correct `--options`.
### Model Creation
- New models → create factories + seeders too. Check `php artisan make:model --help` for options.
## APIs & Eloquent Resources
- Default: Eloquent API Resources + versioning. Match existing app convention if different.
## URL Generation
- Named routes + `route()` function.
## Testing
- Test models → use factories. Check factory custom states before manual setup.
- Faker: `$this->faker->word()` or `fake()->randomDigit()`. Match existing convention.
- `php artisan make:test [options] {name}` for feature tests. `--unit` for unit tests. Most tests = feature.
## Vite Error
- `ViteException: Unable to locate file` → `npm run build` or suggest `npm run dev` / `composer run dev`.
=== pint/core rules ===
# Laravel Pint
- Modified PHP files → run `vendor/bin/pint --dirty --format agent` before finalizing.
- Fix mode only: `vendor/bin/pint --format agent`. No `--test` flag.
=== pest/core rules ===
## Pest
- Pest for testing. Create: `php artisan make:test --pest {name}`.
- `{name}` without test suite dir: `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Run: `php artisan test --compact`. Filter: `--filter=testName`.
- Do NOT delete tests without approval.
</laravel-boost-guidelines>