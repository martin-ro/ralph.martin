# Testing Conventions

**Status:** Ready
**Version:** 1.0
**Last Updated:** 2026-01-20

## Overview

This spec defines how tests should be written throughout the project. All new tests must follow these conventions.

### Goals
- Consistent test style across the codebase
- Readable, maintainable tests
- Fast test execution via parallelization

### Non-Goals
- Defining coverage thresholds (see @AGENT.md)
- Specific test cases for features (see individual specs)

## Conventions

### Test Framework

- **Pest PHP** is the test framework
- Use Pest plugins: `pest-plugin-laravel`, `pest-plugin-livewire`, `pest-plugin-faker`

### Syntax Style

Use a mix of functional and higher-order syntax:

```php
// Higher-order for simple assertions
test('homepage returns ok')->get('/')->assertOk();

test('guest cannot access dashboard')
    ->get('/dashboard')
    ->assertRedirect('/login');

// Functional for complex tests - note blank lines at start and end
test('user can update their profile', function () {

    $user = User::factory()->create();

    $response = $this->actingAs($user)->put('/profile', [
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);

    $response->assertRedirect();
    expect($user->fresh()->name)->toBe('New Name');

});
```

**Rule of thumb:** If the test needs variables, setup, or multiple assertions, use functional syntax.

### Blank Line Formatting

**All functional tests must have a blank line at the beginning and end of the closure:**

```php
// Good
test('user can login', function () {

    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect('/');

});

// Bad - no blank lines
test('user can login', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect('/');
});
```

This applies to `beforeEach()`, `afterEach()`, and `describe()` blocks as well:

```php
beforeEach(function () {

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

});

describe('profile', function () {

    test('can view profile', function () {

        $this->get('/profile')->assertOk();

    });

});
```

### Test Naming

Use `test()` with descriptive strings:

```php
// Good
test('user can login with valid credentials');
test('password reset email is sent');
test('invalid email shows validation error');

// Avoid
it('works');
it('should login');
test_user_can_login(); // snake_case function style
```

### File Organization

Mirror the `app/` directory structure:

```
tests/
├── Feature/
│   ├── Filament/
│   │   ├── Resources/
│   │   │   └── UserResourceTest.php
│   │   └── PanelAuthTest.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── ApiControllerTest.php
│   └── Services/
│       └── PaymentServiceTest.php
├── Unit/
│   ├── Models/
│   │   └── UserTest.php
│   └── Actions/
│       └── CreateOrderActionTest.php
└── Architecture/
    └── ArchitectureTest.php
```

### Grouping with describe()

Only use `describe()` blocks when a file has 5+ related tests:

```php
// Small file - no grouping needed
test('user can login');
test('user can logout');
test('invalid credentials rejected');

// Large file - group related tests
describe('login', function () {

    test('user can login with valid credentials', function () {

        // ...

    });

    test('invalid email shows error', function () {

        // ...

    });

});
```

### Assertions

Use Pest expectations (`expect()`) for all assertions:

```php
// Good
expect($user->name)->toBe('John');
expect($users)->toHaveCount(3);
expect($response->json('data'))->toBeArray();
expect($order->isPaid())->toBeTrue();

// Also acceptable for HTTP response assertions
$response->assertOk();
$response->assertRedirect('/login');
$response->assertSessionHasErrors('email');
```

### Test Data

**Always use factories** for creating test data:

```php
// Good
$user = User::factory()->create();
$users = User::factory()->count(5)->create();
$admin = User::factory()->admin()->create();
$order = Order::factory()->for($user)->create();

// Bad - raw arrays
$user = User::create(['name' => 'Test', 'email' => 'test@test.com', ...]);
```

### Shared Setup

Use `beforeEach()` for common setup across tests in a file:

```php
beforeEach(function () {

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

});

test('user can view dashboard', function () {

    $this->get('/dashboard')->assertOk();

});

test('user can update settings', function () {

    $this->put('/settings', ['timezone' => 'UTC'])->assertOk();

});
```

### Mocking & Fakes

Prefer Laravel fakes over manual mocks:

```php
// Good - Laravel fakes
test('welcome email is sent after registration', function () {

    Mail::fake();

    $this->post('/register', [...]);

    Mail::assertSent(WelcomeEmail::class);

});

test('order notification is queued', function () {

    Queue::fake();

    $order = Order::factory()->create();
    $order->complete();

    Queue::assertPushed(SendOrderNotification::class);

});

// Avoid excessive mocking - only mock external APIs
Http::fake([
    'api.stripe.com/*' => Http::response(['id' => 'ch_123'], 200),
]);
```

### Livewire & Filament Components

Use the Pest Livewire plugin:

```php
use function Pest\Livewire\livewire;

test('can search users', function () {

    User::factory()->create(['name' => 'John Doe']);
    User::factory()->create(['name' => 'Jane Smith']);

    livewire(UserTable::class)
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');

});

test('can create user via form', function () {

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'New User',
            'email' => 'new@example.com',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(User::where('email', 'new@example.com')->exists())->toBeTrue();

});
```

### Database Handling

Database refresh is configured in `tests/Pest.php`. Tests run in parallel - ensure proper isolation:

```php
// Each test should create its own data
test('user count is correct', function () {

    User::factory()->count(3)->create();

    expect(User::count())->toBe(3); // Not affected by other tests

});
```

### Test Independence

Tests must be independent but can share setup via `beforeEach()`:

```php
// Good - shared setup, independent assertions
beforeEach(function () {

    $this->team = Team::factory()->create();

});

test('can add member to team', function () {

    $user = User::factory()->create();

    $this->team->addMember($user);

    expect($this->team->members)->toContain($user);

});

test('can remove member from team', function () {

    $user = User::factory()->create();
    $this->team->addMember($user);

    $this->team->removeMember($user);

    expect($this->team->members)->not->toContain($user);

});
```

## Anti-Patterns

Avoid these patterns:

```php
// Don't use PHPUnit assertions
$this->assertEquals($expected, $actual);  // Use expect()->toBe()
$this->assertTrue($value);                 // Use expect()->toBeTrue()

// Don't create data without factories
User::create(['name' => 'Test', ...]);

// Don't use it() - use test()
it('can login');

// Don't rely on database state from other tests
test('deletes the user created in previous test');

// Don't over-mock
$mock = Mockery::mock(UserRepository::class);
$mock->shouldReceive('find')->andReturn($user);
// Just use the real repository with factory data

// Don't forget blank lines in closures
test('bad formatting', function () {
    $user = User::factory()->create();
});
```

## Acceptance Criteria

- [ ] All new tests follow these conventions
- [ ] Existing tests migrated when touched
- [ ] Test suite runs successfully in parallel (`pest --parallel`)

## Dependencies

- None

## References

- [Pest Documentation](https://pestphp.com/docs/writing-tests)
- [Pest Livewire Plugin](https://pestphp.com/docs/plugins#livewire)
- [Laravel Testing](https://laravel.com/docs/testing)
