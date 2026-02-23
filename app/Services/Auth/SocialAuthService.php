<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialAuthService
{
    /**
     * Find or create a user from a Google OAuth response.
     * Accepts any object that implements getEmail(), getName(), getId()
     * (both real Socialite users and anonymous test fakes).
     * Google guarantees email ownership so we mark email as verified immediately.
     */
    public function findOrCreateFromGoogle(object $googleUser): User
    {
        return User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'              => $googleUser->getName(),
                'google_id'         => $googleUser->getId(),
                'email_verified_at' => now(),
            ]
        );
    }

    /**
     * Log the user into the session and regenerate the session ID.
     */
    public function login(User $user): void
    {
        Auth::login($user, remember: true);
        request()->session()->regenerate();
    }
}
