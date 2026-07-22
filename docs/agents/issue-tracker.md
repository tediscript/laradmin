# Issue tracker: GitHub

Issues and PRDs for this repo live as GitHub issues in `tediscript/laradmin`.
Use the `gh` CLI for all operations; it infers the repo from the clone.

## Conventions

- **A feature's PRD is one issue** with the `prd` label. The body holds the full
  PRD — problem, goal, requirements, and an acceptance-criteria checklist ticked
  off as work lands.
- **Implementation work = child issues** linked to the PRD as GitHub sub-issues.
  Each child puts `Part of #<PRD-number>` at the top of its body.
- **Linking sub-issues:** prefer GitHub native sub-issues. Where unavailable, add
  the child to a task list in the PRD body and keep the `Part of #<PRD>` line.
- **Triage state** = GitHub labels (see `triage-labels.md`), lazy-created on use.

## Day-to-day commands

- **Create:** `gh issue create --title "..." --body "..."` (heredoc for multi-line).
- **Read:** `gh issue view <number> --comments`.
- **List:** `gh issue list --state open --json number,title,labels --jq '.[]|"\(.number)\t\(.title)"'`.
- **Comment:** `gh issue comment <number> --body "..."`.
- **Label:** `gh issue edit <number> --add-label "..."` / `--remove-label "..."`.
- **Close:** `gh issue close <number> --comment "..."`.

## When a skill says "publish to the issue tracker"

`gh issue create`. A PRD gets `--label prd` (create the label first if missing).
A child implementation issue references its PRD with `Part of #<PRD>` and is
linked as a sub-issue.

## When a skill says "fetch the relevant ticket"

`gh issue view <number> --comments`.

## Legacy archive

Specs written before the move to GitHub live under `.scratch/` (e.g.
`.scratch/01-oidc-auth/`). These are **completed, read-only archives** — do not
add to or migrate them. New work goes to GitHub issues.

## Pull requests as a triage surface

**PRs as a request surface: no.** _(Flip to `yes` if external PRs should enter the
triage queue; `/triage` reads this flag.)_
