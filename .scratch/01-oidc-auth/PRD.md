# PRD: OIDC Authentication Support

**Status:** Draft  
**Created:** 2025-05-15  
**Author:** AI-assisted (grilled with user)

---

## Problem

The application currently supports only email+password authentication via Laravel Breeze. Users cannot log in through external identity providers (Google, Authentik, or any generic OIDC provider). This limits flexibility for teams that want SSO or passwordless login.

## Goal

Add OIDC/OAuth2 social login with account linking, ENV-based toggles for each auth method, and smart password handling for OIDC-only users.

## Requirements

### R1: Generic OIDC Provider
- Support any OIDC-compliant identity provider (tested with Authentik)
- Use `kovah/laravel-socialite-oidc` for generic OIDC driver

### R2: Google Login
- Use Laravel Socialite's built-in `google` driver
- Standard OAuth2 flow

### R3: Account Linking (Email Match)
- On OIDC callback, look up existing user by email
- If email exists → link OIDC provider to that user (create `social_accounts` row) and log in
- If email not found → create new user (no password) + create `social_accounts` row + log in
- Auto-set `email_verified_at` on OIDC login (provider already verified email)

### R4: Password Handling for OIDC Users
- Make `users.password` column **nullable** (OIDC-only users have no password)
- Profile password form becomes **smart**:
  - User has password → "Update Password" with current password field required
  - User has no password → "Set Password" with NO current password field
- `PasswordController` skips `current_password` validation when user password is empty

### R5: ENV-Based Auth Toggles
- `AUTH_PASSWORD_LOGIN=true/false` — enables/disables email+password login form + registration page
- `AUTH_GOOGLE=true/false` — enables/disables Google social login button + route
- `AUTH_OIDC=true/false` — enables/disables generic OIDC login button + route
- All toggles are **independent** — any combination is valid
- **Lockout mode**: all three `false` → login page shows "Login is disabled" message
- When `AUTH_PASSWORD_LOGIN=false` → hide registration page entirely (OIDC auto-creates accounts)

### R6: Provider Tracking
- New `social_accounts` table: `id`, `user_id` (FK), `provider`, `provider_user_id`, `name`, `email`, `avatar`, `token`, `refresh_token`, `expires_at`, `timestamps`
- Unique index on `(provider, provider_user_id)`
- Supports multiple providers per user (e.g., Google + Authentik linked to same account)

## Technical Design

### Packages
| Package                        | Purpose                             |
|--------------------------------|-------------------------------------|
| `laravel/socialite` (v5.27)    | Official OAuth1/OAuth2 wrapper      |
| `kovah/laravel-socialite-oidc` | Generic OIDC provider for Socialite |

### ENV Variables
```env
# Auth method toggles
AUTH_PASSWORD_LOGIN=true
AUTH_GOOGLE=true
AUTH_OIDC=true

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

# Generic OIDC
OIDC_CLIENT_ID=
OIDC_CLIENT_SECRET=
OIDC_BASE_URL=
OIDC_REDIRECT_URI=
```

### New Files
| File                                                        | Purpose                            |
|-------------------------------------------------------------|------------------------------------|
| `database/migrations/xxxx_create_social_accounts_table.php` | Provider tracking table            |
| `database/migrations/xxxx_make_users_password_nullable.php` | Allow NULL passwords               |
| `app/Models/SocialAccount.php`                              | Eloquent model for social_accounts |
| `app/Http/Controllers/Auth/SocialLoginController.php`       | Redirect + callback handler        |
| `tests/Feature/SocialLoginTest.php`                         | OIDC login + account linking tests |
| `tests/Feature/PasswordUpdateTest.php`                      | Smart password update tests        |

### Modified Files
| File                                                              | Change                                                       |
|-------------------------------------------------------------------|--------------------------------------------------------------|
| `app/Models/User.php`                                             | Add `socialAccounts()` relation, `hasPassword` accessor      |
| `config/services.php`                                             | Add `google` + `oidc` provider configs                       |
| `app/Http/Controllers/Auth/PasswordController.php`                | Conditional `current_password` validation                    |
| `routes/auth.php`                                                 | Social login routes + ENV-gated registration/password routes |
| `resources/views/auth/login.blade.php`                            | OIDC buttons + ENV-gated password form                       |
| `resources/views/profile/partials/update-password-form.blade.php` | Smart form (Set vs Update)                                   |
| `.env.example`                                                    | New ENV variables                                            |

### Auth Flow
```
User clicks "Login with Google" or "Login with OIDC"
  → Socialite redirects to provider
  → Provider authenticates user, redirects back
  → SocialLoginController@callback($provider)
    → Look up social_accounts by (provider, provider_user_id)
      → Found → log in linked user
      → Not found → look up users by email
        → Found → create social_accounts row → log in
        → Not found → create user (no password, email pre-verified) + social_accounts row → log in
```

## Out of Scope
- Disconnect provider UI
- OIDC token refresh/rotation
- Multiple OIDC providers per user management UI
- Role/permission assignment during OIDC login
- Account merge conflict resolution (e.g., email changed at provider)

## Acceptance Criteria
- [ ] User can log in via Google
- [ ] User can log in via generic OIDC (Authentik)
- [ ] Existing user with matching email gets OIDC provider linked automatically
- [ ] New user is created when no matching email exists
- [ ] OIDC users have `email_verified_at` set automatically
- [ ] OIDC-only user sees "Set Password" form (no current password field)
- [ ] User with password sees "Update Password" form (with current password field)
- [ ] Setting `AUTH_PASSWORD_LOGIN=false` hides password login + registration
- [ ] Setting `AUTH_GOOGLE=false` hides Google button + disables route
- [ ] Setting `AUTH_OIDC=false` hides OIDC button + disables route
- [ ] All three disabled shows "Login is disabled" message
- [ ] Tests cover all auth flows and toggle scenarios