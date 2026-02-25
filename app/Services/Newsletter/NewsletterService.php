<?php

namespace App\Services\Newsletter;

use App\Models\NewsletterSubscriber;
use Illuminate\Support\Str;

class NewsletterService
{
    /**
     * Subscribe a user to the newsletter.
     *
     * @param string $email
     * @return NewsletterSubscriber
     */
    public function subscribe(string $email): NewsletterSubscriber
    {
        // If the user previously unsubscribed and subscribes again, reactivate them.
        $subscriber = NewsletterSubscriber::withTrashed()->firstOrCreate(
            ['email' => $email]
        );

        if (!$subscriber->token) {
            $subscriber->token = Str::random(60);
        }

        $subscriber->is_active = true;
        // If it's the first time or re-subscribing, update subscribed_at
        if (!$subscriber->subscribed_at) {
            $subscriber->subscribed_at = now();
        }
        
        $subscriber->save();

        return $subscriber;
    }

    /**
     * Unsubscribe a user using their unique token.
     *
     * @param string $token
     * @return NewsletterSubscriber
     */
    public function unsubscribe(string $token): NewsletterSubscriber
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();
        
        $subscriber->is_active = false;
        $subscriber->save();

        return $subscriber;
    }
}
