**Status:** done

## Parent

`.scratch/01-oidc-auth/PRD.md`

## What to build

Add a generic OIDC provider option alongside Google. Uses `kovah/laravel-socialite-oidc` to support any OIDC-compliant identity provider (tested with Authentik).

**End-to-end behavior:**
- A "Login with OIDC" button appears on the login page (alongside the Google button from slice 01)
- Clicking it redirects to the configured OIDC provider
- On callback, the same account-linking flow runs: find existing link → find by email → create new user
- The OIDC provider is configured via `OIDC_CLIENT_ID`, `OIDC_CLIENT_SECRET`, `OIDC_BASE_URL` ENV variables
- The existing `SocialLoginController` already handles any provider name, so this slice mainly adds the package, config, and UI button

**Key decisions:**
- The `SocialLoginController` from slice 01 is provider-agnostic — it already accepts `{provider}` as a route parameter
- The OIDC driver is registered as a Socialite provider via `kovah/laravel-socialite-oidc`
- Same `social_accounts` table stores OIDC links with `provider = 'oidc'`

## Acceptance criteria

- [x] `kovah/laravel-socialite-oidc` installed
- [x] OIDC config added to `config/services.php` (client_id, client_secret, redirect, base_url)
- [x] Login page shows "Login with OIDC" button
- [x] OIDC login route works (`login/oidc/redirect`, `login/oidc/callback`)
- [x] Existing user with matching email gets OIDC provider linked automatically
- [x] New user is created when no matching email exists
- [x] `.env.example` updated with `OIDC_CLIENT_ID`, `OIDC_CLIENT_SECRET`, `OIDC_BASE_URL`, `OIDC_REDIRECT_URI`
- [x] Pest tests cover: OIDC redirect, callback with new user, callback with existing user linking

## Blocked by

- `.scratch/01-oidc-auth/issues/01-google-login-foundation.md` — needs SocialLoginController, social_accounts table, User relations