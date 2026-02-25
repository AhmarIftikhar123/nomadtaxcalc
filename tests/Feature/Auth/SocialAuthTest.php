<?php

use Laravel\Socialite\Facades\Socialite;

// ─────────────────────────────────────────────────────────────────────────────
// Google Redirect
// ─────────────────────────────────────────────────────────────────────────────

test('google redirect returns a redirect response', function () {
    Socialite::shouldReceive('driver->redirect')
        ->once()
        ->andReturn(redirect('https://accounts.google.com'));

    $this->get('/auth/google/redirect')->assertRedirect();
});

// ─────────────────────────────────────────────────────────────────────────────
// Google Callback
// ─────────────────────────────────────────────────────────────────────────────

test('google callback creates a new user and logs them in', function () {
    Socialite::shouldReceive('driver->user')
        ->once()
        ->andReturn(fakeGoogleUser());

    $this->get('/auth/google/callback')
         ->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'email'     => 'google@example.com',
        'google_id' => '123456789',
    ]);
});

test('google callback marks email as verified immediately', function () {
    Socialite::shouldReceive('driver->user')
        ->once()
        ->andReturn(fakeGoogleUser('verify@google.com'));

    $this->get('/auth/google/callback');

    expect(\App\Models\User::where('email', 'verify@google.com')->first()->hasVerifiedEmail())
        ->toBeTrue();
});

test('google callback updates existing user with google_id', function () {
    $existing = verifiedUser(['email' => 'existing@google.com']);

    Socialite::shouldReceive('driver->user')
        ->once()
        ->andReturn(fakeGoogleUser('existing@google.com', 'Existing User', '987654321'));

    $this->get('/auth/google/callback');

    expect($existing->fresh()->google_id)->toBe('987654321');
    $this->assertDatabaseCount('users', 1); // no duplicate created
});

// ─────────────────────────────────────────────────────────────────────────────
// Inertia Email Verification Gating
// ─────────────────────────────────────────────────────────────────────────────

test('dashboard redirects unverified user to verification notice', function () {
    $this->actingAs(unverifiedUser())
         ->get('/dashboard')
         ->assertRedirect(route('verification.notice'));
});

test('inertia shares auth.user only for verified users', function () {
    $user = verifiedUser();

    $this->actingAs($user)
         ->get('/dashboard')
         ->assertStatus(200)
         ->assertInertia(fn ($page) => $page->where('auth.user.email', $user->email));
});

test('inertia shares null auth.user for unverified users', function () {
    // Even if actingAs bypasses middleware, the Inertia share() should return null
    $this->actingAs(unverifiedUser())
         ->get('/') // public route — no redirect
         ->assertInertia(fn ($page) => $page->where('auth.user', null));
});
