**Status:** done

## Parent

`.scratch/01-oidc-auth/PRD.md`

## What to build

Implement Google social login end-to-end with account linking. This is the foundation slice that sets up all the shared infrastructure other OIDC slices depend on.

**End-to-end behavior:**
- A "Login with Google" button appears on the login page
- Clicking it redirects to Google for authentication
- On callback, the system finds or creates a local user and links the Google identity
- If a user with the same email already exists → link the Google account to that user and log in
- If no matching email → create a new user (no password, email pre-verified) + link the Google account and log in
- Subsequent logins via Google find the existing linked account directly

**Shared infrastructure created in this slice:**
- `social_accounts` table: `id`, `user_id` (FK), `provider`, `provider_user_id`, `name`, `email`, `avatar`, `token`, `refresh_token`, `expires_at`, `timestamps`. Unique index on `(provider, provider_user_id)`.
- `SocialAccount` Eloquent model
- Migration to make `users.password` nullable (OIDC-only users have no password)
- `socialAccounts()` hasMany relation + `hasPassword` accessor on `User` model
- `SocialLoginController` with `redirect($provider)` and `callback($provider)` actions — provider-agnostic design so adding OIDC later is just adding a config entry
- Social login routes: `GET login/{provider}/redirect` and `GET login/{provider}/callback`
- Google provider config in `config/services.php`
- Google button on `login.blade.php`

**Auth flow:**
```
User clicks "Login with Google"
  → Socialite redirects to Google
  → Google authenticates, redirects back
  → SocialLoginController@callback('google')
    → Look up social_accounts by (provider='google', provider_user_id)
      → Found → log in linked user
      → Not found → look up users by email
        → Found → create social_accounts row → log in
        → Not found → create user (no password, email_verified_at=now) + social_accounts row → log in
```

## Acceptance criteria

- [x] `laravel/socialite` installed
- [x] `social_accounts` migration created with correct columns and unique index on `(provider, provider_user_id)`
- [x] `users.password` column is nullable
- [x] `SocialAccount` model exists with fillable fields and `user()` belongsTo relation
- [x] `User` model has `socialAccounts()` hasMany relation and `hasPassword` accessor
- [x] `SocialLoginController` handles redirect and callback for any provider
- [x] Google config added to `config/services.php`
- [x] Social login routes registered (`login/{provider}/redirect`, `login/{provider}/callback`)
- [x] Login page shows "Login with Google" button
- [x] Existing user with matching email gets Google provider linked automatically
- [x] New user is created when no matching email exists (no password, email pre-verified)
- [x] Already-linked user is logged in directly on subsequent Google logins
- [x] `.env.example` updated with `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET`
- [x] Pest tests cover: new user creation, account linking, existing linked account login

## Blocked by

None - can start immediately.