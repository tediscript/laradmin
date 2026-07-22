# Triage Labels

Maps the five canonical triage roles to this repo's GitHub labels.

| Role (mattpocock/skills) | Label in our tracker | Meaning                                  | Color    |
| ------------------------ | -------------------- | ---------------------------------------- | -------- |
| `needs-triage`           | `needs-triage`       | Maintainer needs to evaluate this issue  | `fbca04` |
| `needs-info`             | `needs-info`         | Waiting on reporter for more information | `5319e7` |
| `ready-for-agent`        | `ready-for-agent`    | Fully specified, ready for an AFK agent  | `0e8a16` |
| `ready-for-human`        | `ready-for-human`    | Requires human implementation            | `d93f0b` |
| `wontfix`                | `wontfix`            | Will not be actioned                     | `ffffff` |

When a skill mentions a role, use the label string from this table.

## Applying labels (lazy-create)

Apply with `gh issue edit <number> --add-label "<label>"`.

This repo **does not pre-create** triage labels. `wontfix` exists by default; the
other four are created on first use. When a role label doesn't exist yet, create
it then apply:

```bash
gh label create "needs-triage" --description "Maintainer needs to evaluate this issue" --color "fbca04"
gh issue edit <number> --add-label "needs-triage"
```

Use the color from the table so labels stay consistent across lazy creates.
