# Laradmin — Laravel Admin Starter Kit

## Purpose

Reusable starter kit for building admin-heavy web applications. Primarily admin panels, sometimes with public-facing pages. Fork this repo to spin up a new project quickly.

## Stack

| Layer         | Technology                                          |
|---------------|-----------------------------------------------------|
| Framework     | Laravel 13 (v13.6.0 skeleton, v13.9.0 framework)    |
| PHP           | 8.5+                                                |
| Frontend      | Blade + Alpine.js + Tailwind CSS                    |
| Asset bundler | Vite 8                                              |
| Auth          | Laravel Breeze (Blade stack)                        |
| Database      | SQLite (default, swap to MySQL/Postgres as needed)  |
| Testing       | Pest 4.x                                            |
| API           | `routes/api.php` ready (add Sanctum for token auth) |

## Glossary

- **Starter kit** — this repo. Clone/fork to start a new admin project.
- **Admin panel** — the primary use case. CRUD-heavy, authenticated, internal tools.
- **Public-facing** — optional front-end pages served alongside the admin panel.

## Project structure

Standard Laravel 13 layout. Key additions:

```
.pi/                 — Pi config: settings, skills, prompts
AGENTS.md            — Project instructions (Boost + agent workflow)
docs/agents/         — Agent documentation (domain, issues, triage)
CONTEXT.md           — This file. Domain context for AI agents.
```

## Decisions log

See `docs/adr/` for architectural decisions (created as needed).