Status: Done

## What to build

Expose the `published_at` field in the post UI so users can view and manually override the publication date (e.g., backdating). Add a datetime-local input to the post form partial. Display the published date in the post index table and the show view. The index view should show the date in a sortable column. The show view should display the formatted date alongside the existing status badge.

## Acceptance criteria

- [x] Post form partial has a `published_at` datetime-local input field
- [x] Post index view shows `published_at` as a sortable column (using existing `$allowedSortColumns`)
- [x] Post show view displays the formatted `published_at` when present
- [x] Empty/null `published_at` displays gracefully (e.g., "—" or hidden)
- [x] `published_at` added to `$allowedSortColumns` in PostController

## Blocked by

- `.scratch/02-published-at-timestamp/issues/01-migration-model-hook-tests.md` — needs the column and model logic