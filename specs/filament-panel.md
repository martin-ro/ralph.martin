# Filament Admin Panel Setup

**Status:** Ready
**Version:** 1.0
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

### Functional Requirements

1. **Panel Configuration**
   - Panel ID: `app`
   - Panel name: `App`
   - Path: `/` (root - this IS the application)
   - Domain: All domains (default)

2. **Authentication**
   - Login page enabled
   - Password reset enabled
   - Registration disabled
   - Email verification disabled
   - Profile page disabled (for now)
   - Auth guard: Default (`web`)
   - Auth model: `App\Models\User`

3. **Access Control**
   - All authenticated users can access the panel
   - Implement `FilamentUser` contract on User model
   - `canAccessPanel()` returns `true` for all users

4. **Styling**
   - Primary color: Blue (`Color::Blue`)
   - Other colors: Filament defaults (Gray, Rose, Emerald, Orange)
   - No custom logo (use app name as text)
   - No custom favicon (use Laravel default)

### Non-Functional Requirements

- PHP 8.4+ (already met per composer.json)
- Laravel 12+ (already met per composer.json)

## Architecture

### Files Created/Modified

```
app/
├── Models/
│   └── User.php                    # Add FilamentUser contract
└── Providers/
    └── Filament/
        └── AppPanelProvider.php    # Panel configuration

bootstrap/
└── providers.php                   # Register AppPanelProvider

config/
└── filament.php                    # Published config (optional)

tests/
└── Feature/
    └── Filament/
        └── PanelAuthTest.php       # Authentication tests
```

### Panel Provider Structure

```php
// app/Providers/Filament/AppPanelProvider.php
use Filament\Panel;
use Filament\Support\Colors\Color;

public function panel(Panel $panel): Panel
{
    return $panel
        ->id('app')
        ->path('/')
        ->login()
        ->passwordReset()
        ->colors([
            'primary' => Color::Blue,
        ])
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
        ->middleware([...])
        ->authMiddleware([...]);
}
```

## Testing Requirements

Feature tests must be created at `tests/Feature/Filament/PanelAuthTest.php` to verify the panel setup.

### Required Test Cases

```php
// tests/Feature/Filament/PanelAuthTest.php

it('redirects unauthenticated users to login', function () {
    $this->get('/')
        ->assertRedirect('/login');
});

it('renders the login page', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Sign in');
});

it('allows a user to login', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors();

    $this->assertGuest();
});

it('renders the password reset request page', function () {
    $this->get('/password-reset')
        ->assertOk();
});

it('sends password reset email', function () {
    $user = User::factory()->create();

    $this->post('/password-reset', [
        'email' => $user->email,
    ])->assertSessionHasNoErrors();
});

it('shows the dashboard after login', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertSee('Dashboard');
});

it('allows any authenticated user to access the panel', function () {
    $user = User::factory()->create();

    expect($user->canAccessPanel(Filament::getPanel('app')))->toBeTrue();
});

it('allows user to logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect('/login');

    $this->assertGuest();
});
```

### Test Coverage Requirements

- All test cases above must pass
- Tests must use Pest syntax
- No additional coverage threshold for this spec (setup only)

## Implementation Steps

1. Install Filament via Composer
   ```bash
   composer require filament/filament:"^5.0"
   ```

2. Run Filament install command
   ```bash
   php artisan filament:install --panels
   ```

3. Rename/reconfigure panel provider
   - Rename `AdminPanelProvider` to `AppPanelProvider`
   - Update panel ID from `admin` to `app`
   - Change path from `/admin` to `/`

4. Configure authentication features
   - Add `->login()` and `->passwordReset()`
   - Remove or don't add `->registration()`, `->emailVerification()`, `->profile()`

5. Configure colors
   - Add `->colors(['primary' => Color::Blue])`

6. Update User model
   - Implement `Filament\Models\Contracts\FilamentUser`
   - Add `canAccessPanel()` method returning `true`

7. Verify provider registration in `bootstrap/providers.php`

8. Create test file at `tests/Feature/Filament/PanelAuthTest.php`

9. Run tests to verify implementation
   ```bash
   composer test
   ```

## Acceptance Criteria

- [ ] Visiting `/` redirects to login when unauthenticated
- [ ] Login page renders at `/login`
- [ ] Password reset flow works via `/password-reset`
- [ ] After login, user sees the Filament dashboard at `/`
- [ ] Primary color throughout UI is blue
- [ ] Any authenticated user can access the panel
- [ ] All feature tests in `PanelAuthTest.php` pass
- [ ] All existing tests pass
- [ ] PHPStan analysis passes

## Dependencies

- None (first spec)

## References

- [Filament Installation](https://filamentphp.com/docs/5.x/introduction/installation)
- [Panel Configuration](https://filamentphp.com/docs/5.x/panels/configuration)
- [Authentication](https://filamentphp.com/docs/5.x/panels/users)
