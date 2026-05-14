# Skill Rules

## Slash Commands

User types `/<skill>` → explicit invoke. Match `.agents/skills/<skill-name>/SKILL.md`. Folder w/ `SKILL.md` = valid skill.

## Activation Announcement

**ALWAYS** lead response:

```
🔧 Skill activated: **triage**
```

Always:
- `/command` used (e.g., `/triage`, `/tdd`, `/diagnose`)
- NL inference (e.g., "triage this bug", "use TDD")

## NL Matching

Request matches skill purpose → activate + announce. No exceptions.

## Always-Active

Active w/o invocation:

### smart-commit

Never `git add .` / `git add -A` / `git add --all`. Stage specific files. Group related changes. See `.agents/skills/smart-commit/SKILL.md`.

## Skill Discovery

Unsure → list `.agents/skills/`.