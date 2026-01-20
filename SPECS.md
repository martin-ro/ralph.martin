# Specification Writing Guide

## Why We Write Specs

Specs are the source of truth for what to build. They exist to:

1. **Define before building** - Requirements are decided upfront, not discovered during implementation
2. **Create determinism** - Ralph can work autonomously because specs remove ambiguity
3. **Enable acceptance testing** - Clear criteria to know when something is done
4. **Persist knowledge** - Decisions survive across Ralph loops and sessions

Without specs, Ralph guesses. With specs, Ralph executes.

## Spec Workflow

```
Draft → Ready → In Progress → Implemented
```

- **Draft**: Spec is being written, not ready for implementation
- **Ready**: Spec is approved, Ralph can pick it up
- **In Progress**: Implementation has started
- **Implemented**: Feature complete, tests passing

Update status in `specs/README.md` when state changes.

## Creating a New Spec

### 1. Interview First

Before writing a spec, gather requirements through questions:

- What problem does this solve?
- Who uses this feature?
- What are the inputs/outputs?
- What should NOT be included? (non-goals)
- How do we know it's working? (acceptance criteria)

Use `AskUserQuestion` tool to clarify before writing.

### 2. Spec Structure

```markdown
# Feature Name

**Status:** Draft
**Version:** 1.0
**Last Updated:** YYYY-MM-DD

## Overview

Brief description of what this feature does.

### Goals
- What this spec will accomplish
- Keep it focused

### Non-Goals
- What this spec explicitly won't do
- Prevents scope creep

## Requirements

### Functional Requirements

Describe what the system must do:
- User can X
- System does Y when Z
- Data is stored as...

### Non-Functional Requirements

Performance, security, compatibility constraints.

## Implementation Steps

High-level steps (not code):
1. First thing to do
2. Second thing to do
3. etc.

## Acceptance Criteria

Checkboxes that define "done":
- [ ] User can do X
- [ ] System responds with Y
- [ ] Tests cover all criteria above
- [ ] All tests pass

## Dependencies

Links to other specs this depends on:
- [other-spec.md](other-spec.md) - Why it's needed

## References

External docs, links, resources.
```

### 3. Keep It Lean

- **No code examples** unless absolutely necessary for clarity
- **No implementation details** - Ralph figures those out
- **Focus on WHAT, not HOW**
- **Acceptance criteria are testable** - if you can't test it, rewrite it

## Specs to @fix_plan.md

When a spec is marked **Ready**:

1. Ralph reads the spec
2. Ralph creates tasks in `@fix_plan.md` based on implementation steps
3. Ralph marks spec as **In Progress**
4. Ralph works through tasks
5. When all acceptance criteria pass, Ralph marks spec **Implemented**

Ralph does NOT invent tasks. Tasks come from specs.

## Examples

### Good Acceptance Criteria

```markdown
- [ ] Visiting `/` redirects to login when unauthenticated
- [ ] Login page renders at `/login`
- [ ] User can login with valid credentials
- [ ] Invalid credentials show error message
- [ ] Feature tests cover all criteria above
```

### Bad Acceptance Criteria

```markdown
- [ ] Login works
- [ ] Good UX
- [ ] Fast performance
- [ ] Clean code
```

(Too vague - can't be tested objectively)

## Testing Convention Reference

All specs that involve code changes should include:

```markdown
- [ ] Feature tests cover all acceptance criteria above
- [ ] All tests pass (`composer test`)
```

Tests must follow `specs/testing-conventions.md`.

## When to Write a Spec

Write a spec when:
- Adding a new feature
- Changing existing behavior significantly
- Setting up infrastructure/tooling
- Anything that needs clear acceptance criteria

Don't need a spec for:
- Bug fixes (unless complex)
- Typo corrections
- Dependency updates
- Simple refactors
