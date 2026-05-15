# 2. Admin Route Prefix by Default

Date: 2026-05-15

## Status

Accepted

## Context

The admin panel has CRUD resources for Posts, Users, Roles, and Permissions, plus user profile management. All authenticated routes live under the `admin/` URL prefix with `auth` middleware. CRUD resources also use permission-based authorization.

We need a convention for where new authenticated resources are placed.

## Decision

By default, all new authenticated resources are placed behind the `admin/` route prefix with `auth` middleware and permission-based access control.

If a future feature needs to be outside `admin/` (e.g., a public user dashboard, API endpoints), that decision is made explicitly and documented.

### Current structure

```
/admin/posts          — Post CRUD (auth + permission)
/admin/users          — User CRUD (auth + permission)
/admin/roles          — Role CRUD (auth + permission)
/admin/permissions    — Permission CRUD (auth + permission)
/admin/profile        — User profile (auth only)
/                     — Welcome page (public)
```

### Route group pattern

```php
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::middleware('permission:resource.view')->group(function () {
        // Index routes
    });
    // ... other CRUD routes with permission middleware
});
```

## Consequences

- New resources default to `admin/` — no decision needed unless breaking the pattern
- Breaking the pattern requires explicit justification
