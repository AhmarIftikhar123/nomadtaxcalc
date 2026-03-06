<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Services\Contact\ContactService;
use Inertia\Inertia;

class LandingPageController extends Controller
{
    /**
     * Display the home/landing page
     */
    public function index()
    {
        $landingData = [
            'features' => $this->getFeatures(),
            'destinations' => $this->getPopularDestinations(),
            'testimonials' => $this->getTestimonials(),
            'howItWorks' => $this->getHowItWorks(),
        ];

        return Inertia::render('Landing/Index', $landingData);
    }

    /**
     * Display the Privacy Policy page (public, no auth required)
     */
    public function privacyPolicy()
    {
        return Inertia::render(
            'PrivacyPolicy/Index',
            ['mailConfig' => [
                'from' => config('mail.from.address'),
            ]]
        );
    }

    /**
     * Display the About page.
     */
    public function about()
    {
        return Inertia::render('About/Index');
    }

    /**
     * Display the Contact page.
     */
    public function contact()
    {
        return Inertia::render('Contact/Index');
    }

    /**
     * Handle the contact form submission.
     */
    public function submitContact(ContactRequest $request, ContactService $service)
    {
        $service->handleSubmission($request->validated(), $request);

        return back()->with('success', 'Your message has been sent successfully. We will get back to you soon.');
    }

    /**
     * Get key features
     */
    private function getFeatures(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Multi-Country Calculator',
                'description' => 'Handle complex tax liability across dozens of jurisdictions simultaneously with real-time exchange rates.',
                'icon' => 'language',
            ],
            [
                'id' => 2,
                'title' => '183-Day Rule Tracker',
                'description' => 'Automated residency monitoring using your travel history to ensure you never accidentally trigger tax residency.',
                'icon' => 'schedule',
            ],
            [
                'id' => 3,
                'title' => 'US FEIE Calculator',
                'description' => 'Specialized Foreign Earned Income Exclusion tools for American expats to maximize savings legally.',
                'icon' => 'payments',
            ],
        ];
    }

    /**
     * Get how it works steps
     */
    private function getHowItWorks(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Enter Your Income',
                'description' => 'Input your global earnings, income sources, and current tax residency status. Our engine handles multi-currency conversions automatically.',
                'icon' => 'payments',
            ],
            [
                'id' => 2,
                'title' => 'Add Countries',
                'description' => 'Select up to 5 destinations from our database of 150+ countries. Compare tax brackets, social security contributions, and digital nomad visa costs side-by-side.',
                'icon' => 'public',
            ],
            [
                'id' => 3,
                'title' => 'Get Your Strategy',
                'description' => 'Receive a comprehensive breakdown of your projected net income, local tax obligations, and a step-by-step visa application guide in one PDF.',
                'icon' => 'description',
            ],
        ];
    }

    /**
     * Get popular destinations
     */
    private function getPopularDestinations(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Portugal',
                'taxRate' => '12.5-48% Tax',
                'visa' => 'NHR Visa',
                'flag' => 'pt',
            ],
            [
                'id' => 2,
                'name' => 'Spain',
                'taxRate' => '24% Fixed Tax',
                'visa' => 'Digital Nomad',
                'flag' => 'es',
            ],
            [
                'id' => 3,
                'name' => 'UAE',
                'taxRate' => '0% Income Tax',
                'visa' => 'Remote Work',
                'flag' => 'ae',
            ],
            [
                'id' => 4,
                'name' => 'Mexico',
                'taxRate' => '0-35% Tax',
                'visa' => 'Residency',
                'flag' => 'mx',
            ],
            [
                'id' => 5,
                'name' => 'Thailand',
                'taxRate' => '0-35% Tax',
                'visa' => 'LTR Visa',
                'flag' => 'th',
            ],
            [
                'id' => 6,
                'name' => 'Georgia',
                'taxRate' => '1% Tax',
                'visa' => 'Entrepreneur',
                'flag' => 'ge',
            ],
            [
                'id' => 7,
                'name' => 'Estonia',
                'taxRate' => '20% Tax',
                'visa' => 'e-Residency',
                'flag' => 'ee',
            ],
            [
                'id' => 8,
                'name' => 'Malta',
                'taxRate' => '0-35% Tax',
                'visa' => 'Digital Nomad',
                'flag' => 'mt',
            ],
        ];
    }

    /**
     * Get testimonials
     */
    private function getTestimonials(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Alex Rivera',
                'role' => 'Full-stack Developer',
                'testimonial' => 'NomadTax saved me thousands in Portugal. The NHR guide was worth every penny.',
                'rating' => 5,
                'avatar' => 'alex',
            ],
            [
                'id' => 2,
                'name' => 'Sarah Chen',
                'role' => 'UX Designer',
                'testimonial' => 'I finally understand my tax liability across three countries. Essential tool for nomads.',
                'rating' => 5,
                'avatar' => 'sarah',
            ],
            [
                'id' => 3,
                'name' => 'James Wright',
                'role' => 'Content Creator',
                'testimonial' => 'The UAE visa guide made my transition seamless. Highly recommend NomadTax.',
                'rating' => 5,
                'avatar' => 'james',
            ],
        ];
    }
}
