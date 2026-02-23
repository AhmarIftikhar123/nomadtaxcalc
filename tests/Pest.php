<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| Shared helpers available globally to all Feature tests.
| Add frequently-used factory shortcuts and mock helpers here to keep
| individual test files DRY.
|
*/

/**
 * Create a fully verified user (default factory state).
 */
function verifiedUser(array $attrs = []): \App\Models\User
{
    return \App\Models\User::factory()->create($attrs);
}

/**
 * Create an unverified user (email_verified_at = null).
 */
function unverifiedUser(array $attrs = []): \App\Models\User
{
    return \App\Models\User::factory()->unverified()->create($attrs);
}

/**
 * Return a fake Socialite Google user object for mocking.
 */
function fakeGoogleUser(
    string $email = 'google@example.com',
    string $name  = 'Google User',
    string $id    = '123456789'
): object {
    return new class($email, $name, $id) {
        public function __construct(
            private readonly string $email,
            private readonly string $name,
            private readonly string $id,
        ) {}

        public function getEmail(): string { return $this->email; }
        public function getName(): string  { return $this->name; }
        public function getId(): string    { return $this->id; }
    };
}

