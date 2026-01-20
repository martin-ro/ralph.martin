# Agent Build Instructions

## Project Setup
```bash
composer install
npm install
```

## Running Tests
```bash
# Full test suite (lint, refactor, types, unit)
composer test

# Individual test commands
composer test:lint      # Pint + Rector dry-run + npm lint
composer test:refactor  # Rector dry-run
composer test:types     # PHPStan analysis
composer test:arch      # Architecture tests (Pest)
composer test:unit      # Unit/Feature tests (Pest --parallel)
```

## Code Formatting
```bash
# Auto-fix linting issues
composer lint           # Rector + Pint + npm lint

# Check without fixing
composer test:lint
```

## Build Commands
```bash
# Production build (frontend assets)
npm run build
```

## Development Server
Herd/Valet handles local serving - no explicit serve command needed.

For full dev environment with queue, logs, and Vite:
```bash
composer dev
```

## Key Learnings
- Tests run in parallel via Pest - ensure database isolation
- Update this section when you learn new build optimizations
- Document any gotchas or special setup requirements

## Specifications

**Lookup table**: `specs/README.md`

Before implementing any feature:
1. Check `specs/README.md` for existing specs
2. Read relevant spec files for requirements and acceptance criteria
3. Reference implemented specs when building on existing features

Specs marked as **Implemented** contain working code you can reference and extend.

## Feature Development Quality Standards

**CRITICAL**: All new features MUST meet the following mandatory requirements before being considered complete.

### Testing Requirements

- **Test Pass Rate**: 100% - all tests must pass, no exceptions
- **Test Types Required**:
  - Unit tests for business logic and services
  - Feature tests for HTTP endpoints and controllers
  - Architecture tests for structural constraints
- **Test Quality**: Tests must validate behavior, not just exist
- **Validation**: Run `composer test` before marking features complete

### Git Workflow Requirements

Before moving to the next feature, ALL changes must be:

1. **Feature Branches**:
   - Create a feature branch from `main` for each new feature
   - Branch naming: `feature/<feature-name>`, `fix/<issue-name>`
   ```bash
   git checkout main
   git pull origin main
   git checkout -b feature/<feature-name>
   ```

2. **Committed with Clear Messages**:
   ```bash
   git add .
   git commit -m "feat(module): descriptive message following conventional commits"
   ```
   - Use conventional commit format: `feat:`, `fix:`, `docs:`, `test:`, `refactor:`
   - Include scope when applicable: `feat(api):`, `fix(ui):`, `test(auth):`
   - Write descriptive messages that explain WHAT changed and WHY

3. **Pull Request When Complete**:
   ```bash
   git push -u origin feature/<feature-name>
   gh pr create --title "feat: description" --body "Summary of changes"
   ```
   - Open PR only after all tests pass
   - PR targets `main` branch

4. **Ralph Integration**:
   - Pick the next task from @fix_plan.md
   - Mark items complete in @fix_plan.md upon completion
   - Update spec status in `specs/README.md` when feature is implemented
   - Update PROMPT.md if development patterns change

### Documentation Requirements

**ALL implementation documentation MUST remain synchronized with the codebase**:

1. **Code Documentation**:
   - PHPDoc blocks for public methods and complex logic
   - Update inline comments when implementation changes
   - Remove outdated comments immediately

2. **Implementation Documentation**:
   - Update relevant sections in this @AGENT.md file
   - Keep build and test commands current
   - Document breaking changes prominently

3. **@AGENT.md Maintenance**:
   - Add new build patterns to relevant sections
   - Update "Key Learnings" with new insights
   - Keep command examples accurate and tested

### Feature Completion Checklist

Before marking ANY feature as complete, verify:

- [ ] All tests pass (`composer test`)
- [ ] Code formatted (`composer lint`)
- [ ] All changes committed with conventional commit messages
- [ ] Feature branch pushed to remote
- [ ] Pull request created targeting `main`
- [ ] @fix_plan.md task marked as complete
- [ ] Spec status updated in `specs/README.md` (if implementing a spec)
- [ ] @AGENT.md updated (if new patterns introduced)

### Rationale

These standards ensure:
- **Quality**: Test pass rates prevent regressions
- **Traceability**: Git commits and @fix_plan.md provide clear history
- **Review**: PRs enable code review before merging to main
- **Maintainability**: Current documentation reduces onboarding time
- **Automation**: Ralph integration ensures continuous development practices

**Enforcement**: AI agents should automatically apply these standards to all feature development tasks without requiring explicit instruction for each task.
