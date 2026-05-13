---
name: smart-commit
description: >
  Commit only related files in logical groups. Each commit stages only files that
  belong together — never uses git add . or git add -A. Use when committing changes,
  creating a commit, staging files, or user says "commit", "smart commit", or /smart-commit.
---

# Smart Commit

## Rules

1. **Never** use `git add .`, `git add -A`, or `git add --all`
2. **Always** use `git add <specific-file>` for each file
3. Group related files into separate commits
4. Each commit gets a clear, descriptive message

## Workflow

### 1. Analyze

Run `git status` and review all changed/untracked files.

### 2. Group

Read the diff of each file (`git diff <file>` and/or `git diff --cached <file>`) and group files by logical concern:

- Model + its migration + its factory → one commit
- Route changes + controller → one commit
- Blade template + its CSS/JS → one commit
- Config/doc changes → separate commits
- Unrelated fixes → separate commits

### 3. Confirm

Show the user the proposed grouping before staging:

```
Commit 1: User model + users migration + UserFactory
Commit 2: DashboardController + dashboard route
Commit 3: README update
```

Ask: "Proceed with these groups?"

### 4. Commit

For each group:

1. `git add <file1> <file2> ...` (explicit files only)
2. `git commit -m "<descriptive message>"`
3. Confirm success before moving to next group

### 5. Summary

After all commits, show `git log --oneline -N` for the user to verify.