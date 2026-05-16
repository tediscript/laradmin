**Status:** done

## Parent

`.scratch/01-oidc-auth/PRD.md`

## What to build

Add ENV-based toggles that let the administrator enable or disable each authentication method independently: password login, Google, and OIDC. The login page and routes adapt based on which methods are active.

**End-to-end behavior:**
- Three new ENV variables: `AUTH_PASSWORD_LOGIN`, `AUTH_GOOGLE`, `AUTH_OIDC` — each `true` or `false`
- All toggles are independent — any combination is valid
- `AUTH_PASSWORD_LOGIN=false` → login page hides the email+password form, registration page is hidden entirely (OIDC auto-creates accounts)
- `AUTH_GOOGLE=false` → Google button hidden on login page, `login/google/*` routes return 404
- `AUTH_OIDC=false` → OIDC button hidden on login page, `login/oidc/*` routes return 404
- Lockout mode: all three `false` → login page shows "Login is disabled" message, no login methods visible
- All three `true` (default) → everything visible as expected

**Key decisions:**
- ENV checks happen at route level (middleware or route closure) for hard gating, and at Blade level for UI visibility
- Social login routes should return 404 (not 403) when disabled — they shouldn't appear to exist
- Registration route is gated alongside password login — if password login is off, there's no reason for registration (OIDC creates accounts automatically)

## Acceptance criteria

- [x] `AUTH_PASSWORD_LOGIN` ENV var controls visibility of email+password login form
- [x] `AUTH_PASSWORD_LOGIN=false` hides the registration page entirely
- [x] `AUTH_GOOGLE` ENV var controls visibility of Google login button
- [x] `AUTH_GOOGLE=false` makes `login/google/*` routes return 404
- [x] `AUTH_OIDC` ENV var controls visibility of OIDC login button
- [x] `AUTH_OIDC=false` makes `login/oidc/*` routes return 404
- [x] All three disabled shows "Login is disabled" message on login page
- [x] Default values in `.env.example`: all three set to `true`
- [x] Pest tests cover: all enabled, only password, only Google, only OIDC, lockout mode, registration hidden when password off

## Blocked by

- `.scratch/01-oidc-auth/issues/01-google-login-foundation.md` — needs social login routes and login page with provider buttons