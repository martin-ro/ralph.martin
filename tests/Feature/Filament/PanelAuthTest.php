<?php

use App\Models\User;
use Filament\Facades\Filament;

beforeEach(function () {

    Filament::setCurrentPanel(
        Filament::getPanel('app'),
    );

});

test('login page renders successfully')
    ->get('/login')
    ->assertOk()
    ->assertSee('Sign in');

test('password reset request page renders successfully')
    ->get('/password-reset/request')
    ->assertOk();

test('authenticated user can access dashboard', function () {

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertSee('Dashboard');

});

test('unauthenticated user accessing dashboard is redirected to login', function () {

    $this->get('/')
        ->assertRedirect('/login');

});

test('user can logout', function () {

    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect('/login');

    $this->assertGuest();

});

test('any user can access the panel', function () {

    $user = User::factory()->create();

    expect($user->canAccessPanel(Filament::getPanel('app')))->toBeTrue();

});
