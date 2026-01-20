# Filament Admin Panel Setup

**Status:** Ready
**Version:** 1.1
**Last Updated:** 2026-01-20

## Overview

Install and configure Filament v5 as the primary application interface.

### Goals
- Install Filament Panel Builder
- Configure panel at root path (`/`) as the main application entry point
- Set up authentication with login and password reset
- Apply custom color scheme

### Non-Goals
- Multi-tenancy support
- Custom theme compilation
- Custom logo/branding assets
- Initial CRUD resources (separate specs)

## Requirements

### Panel Configuration

- Panel ID: `app`
- Panel name: `App`
- Path: `/` (root - this IS the application)
- Domain: All domains (default)

### Authentication

- Login page enabled
- Password reset enabled
- Registration disabled
- Email verification disabled
- Profile page disabled
- Auth guard: Default (`web`)
- Auth model: `App\Models\User`

### Access Control

- All authenticated users can access the panel
- Implement `FilamentUser` contract on User model
- `canAccessPanel()` returns `true` for all users

### Styling

- Primary color: Blue
- Gray color: Slate
- Other colors: Filament defaults

## Implementation Steps

1. Install Filament via Composer
2. Run Filament panel install command
3. Reconfigure generated panel provider (rename to `AppPanelProvider`, set ID/path/name)
4. Configure authentication features (login, password reset only)
5. Configure primary color to Blue
6. Update User model to implement `FilamentUser` contract
7. Verify provider registration
8. Create initial admin user via artisan

## Acceptance Criteria

- [ ] Visiting `/` redirects to login when unauthenticated
- [ ] Login page renders at `/login`
- [ ] Password reset flow works
- [ ] After login, user sees the Filament dashboard
- [ ] Primary color throughout UI is blue
- [ ] Any authenticated user can access the panel
- [ ] Feature tests cover all acceptance criteria above
- [ ] All tests pass (`composer test`)

## Dependencies

- [testing-conventions.md](testing-conventions.md) - Tests must follow project conventions

## References

- [Filament Installation](https://filamentphp.com/docs/5.x/introduction/installation)
- [Panel Configuration](https://filamentphp.com/docs/5.x/panels/configuration)
- [Authentication](https://filamentphp.com/docs/5.x/panels/users)
