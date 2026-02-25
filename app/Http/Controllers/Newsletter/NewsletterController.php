<?php

namespace App\Http\Controllers\Newsletter;

use App\Http\Controllers\Controller;
use App\Services\Newsletter\NewsletterService;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Store a new newsletter subscription.
     */
    public function subscribe(Request $request, NewsletterService $newsletterService)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $newsletterService->subscribe($request->email);

        return back()->with('success', 'You have been subscribed to our newsletter!');
    }

    /**
     * Unsubscribe from the newsletter via token.
     */
    public function unsubscribe($token, NewsletterService $newsletterService)
    {
        $newsletterService->unsubscribe($token);

        return redirect('/')->with('success', 'You have been successfully unsubscribed from the newsletter.');
    }
}
