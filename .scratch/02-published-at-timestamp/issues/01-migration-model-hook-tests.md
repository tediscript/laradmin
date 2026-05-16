Status: Done

## What to build

Add a nullable `published_at` timestamp column to the posts table. When a post's `published` boolean flips from `false` to `true`, automatically set `published_at` to the current timestamp — but only if `published_at` is not being manually changed in the same save. When `published` flips back to `false`, leave `published_at` untouched. This preserves the original publication date across draft/publish cycles.

The model hook must detect: (1) first publish → auto-set `published_at`, (2) unpublish → no change, (3) re-publish → no change (already set), (4) manual backdate → honor the user's value.

Update the PostFactory `published()` state to set `published_at = now()`, and `unpublished()` to set `published_at = null`.

## Acceptance criteria

- [x] Migration adds nullable `published_at` timestamp to posts table
- [x] `published_at` is in Post's `$fillable` and cast as `datetime`
- [x] Model `booted()` hook auto-sets `published_at = now()` when `published` goes `false → true` and `published_at` is not dirty
- [x] Unpublishing (`published` goes `true → false`) does not alter `published_at`
- [x] Re-publishing (`published` goes `false → true` when `published_at` is already set) preserves the original `published_at`
- [x] Manually setting `published_at` (backdate) is honored — hook does not overwrite it
- [x] PostFactory `published()` state sets `published_at`, `unpublished()` sets it `null`
- [x] All tests pass

## Blocked by

None — can start immediately.