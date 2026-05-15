# 0003 — Timezone Handling

**Date:** 2026-05-15

## Status

Accepted

## Context

The application needs to display timestamps in the user's local timezone. As a generic starter kit, it may be deployed in any region and serve users across multiple timezones.

Key considerations:
- Accounting apps need correct cut-off dates per timezone
- MySQL `datetime` columns don't store timezone offset
- ISO 8601 strings lose MySQL date function support

## Decision

1. **Store all timestamps in UTC** — `APP_TIMEZONE` defaults to `UTC` in `.env`, configurable via `APP_TIMEZONE` env var
2. **Per-user timezone** — `timezone` column on `users` table (nullable string). When null, falls back to `config('app.timezone')`
3. **Blade `@datetime()` directive** — Converts timestamps to the authenticated user's display timezone. Falls back to `config('app.timezone')` for unauthenticated views
4. **Profile timezone selector** — Users pick their timezone from `DateTimeZone::listIdentifiers()`, grouped by region
5. **No ISO storage** — Standard MySQL `datetime` columns. The timezone is a known constant (UTC), no offset needed

## Consequences

- All DB queries on timestamps are consistent (always UTC)
- Users see dates in their local timezone
- New users default to the app's configured timezone
- Changing `APP_TIMEZONE` after deployment does not affect existing timestamps (always UTC)