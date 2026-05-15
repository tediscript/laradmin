# 1. All Index Tables Must Be Searchable and Sortable

Date: 2026-05-15

## Status

Accepted

## Context

Every resource index page in the admin panel displays a paginated table of records. Users need to find specific records quickly, especially as data grows. Without search and sort, users must scan pages manually.

All resource index pages (Posts, Users, Roles, Permissions) now implement server-side search and sort. This pattern should be applied consistently to any future resource index tables.

## Decision

Every index table in the admin panel MUST include:

1. **Search** — A search input with a magnifying glass icon, filtering by the resource's primary text field(s). Uses `?search=` query parameter with `LIKE` matching.

2. **Sortable columns** — All data columns (including aggregate counts) must be clickable column headers that toggle ascending/descending sort. Uses `?sort_by=` and `sort_direction=` query parameters.

3. **Pagination preserving query params** — Pagination links must preserve search/sort state via `->appends(request()->query())`.

### Implementation pattern

**Controller:**
```php
$sortBy = request('sort_by', 'created_at');
$sortDirection = request('sort_direction', 'desc');
$search = request('search');

$allowedSortColumns = ['name', 'created_at', /* relation_count columns */];

// Validate inputs, fallback to defaults if invalid

$query = Model::query()
    ->with(/* relations */)
    ->withCount(/* count relations for sortable aggregate columns */)
    ->when($search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
    ->orderBy($sortBy, $sortDirection)
    ->paginate(10)
    ->appends(request()->query());
```

**Blade view:**
- Search form with hidden sort params, clear link when searching
- Column headers as clickable `<a>` tags with ↑/↓/⇅ direction indicators
- Aggregate count columns (e.g., "5 permissions") use `withCount` values, not collection `->count()`
- Empty state differentiates between "no records at all" and "no search results"

**Tests:**
- Sort each column ascending/descending
- Search by primary field
- Search with no results shows message
- Invalid sort column falls back to default

## Consequences

- Any new resource index page MUST include search and sort from the start
- All sortable columns must be whitelisted in `$allowedSortColumns` (security)
- Aggregate columns (counts from relationships) use `withCount` for performance (avoids N+1)
- Sort/search state persists across pagination