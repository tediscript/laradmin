# Skill Invocation Rules

## Slash Command Convention

When the user types a message starting with `/` followed by a skill name, treat it as an explicit skill invocation command. Match it to the corresponding skill name found in `.agents/skills/` and announce activation.

**Available skills** are determined dynamically by scanning `.agents/skills/<skill-name>/SKILL.md`. Any folder under `.agents/skills/` containing a `SKILL.md` is a valid skill.

## Activation Announcement

**ALWAYS** announce skill activation at the very start of your response using this format:

```
🔧 Skill activated: **triage**
```

This applies in ALL cases:
- When the user uses a `/command` explicitly (e.g., `/triage`, `/tdd`, `/diagnose`)
- When you infer a skill should be used from natural language (e.g., "triage this bug", "use TDD", "diagnose this")

## Natural Language Matching

If the user doesn't use a `/command` but their request clearly matches a skill's purpose, activate the skill AND announce it. Always announce the skill even when inferred from natural language.

## Skill Discovery

If unsure which skills are available, list `.agents/skills/` to find all installed skills.
