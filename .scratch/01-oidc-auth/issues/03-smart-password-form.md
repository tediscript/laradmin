**Status:** done

## Parent

`.scratch/01-oidc-auth/PRD.md`

## What to build

Make the profile password form context-aware: users who registered via OIDC (no password) see "Set Password" without a current password field, while users with an existing password see "Update Password" with the current password field required.

**End-to-end behavior:**
- OIDC-only user navigates to profile → sees "Set Password" section with just `password` + `password_confirmation` fields (no "current password" field)
- User with password navigates to profile → sees "Update Password" section with `current_password` + `password` + `password_confirmation` fields
- OIDC-only user sets a password → from then on, they see "Update Password" with current password required
- `PasswordController` validates `current_password` only when the user actually has a password

**Key decisions:**
- The `hasPassword` accessor on `User` (from slice 01) determines which UI to show
- The controller conditionally adds the `current_password` validation rule
- No separate "set password" route — the same `PasswordController@update` handles both cases

## Acceptance criteria

- [x] `PasswordController` skips `current_password` validation when `auth()->user()->hasPassword` is false
- [x] Profile password form shows "Set Password" heading when user has no password
- [x] Profile password form shows "Update Password" heading when user has a password
- [x] Current password field is hidden entirely when user has no password
- [x] OIDC-only user can set a password without providing current password
- [x] After setting a password, the form switches to "Update Password" mode on next page load
- [x] User with password still must provide current password to change it
- [x] Pest tests cover: set password (no current), update password (current required), validation errors for both paths

## Blocked by

- `.scratch/01-oidc-auth/issues/01-google-login-foundation.md` — needs nullable password migration, `hasPassword` accessor on User