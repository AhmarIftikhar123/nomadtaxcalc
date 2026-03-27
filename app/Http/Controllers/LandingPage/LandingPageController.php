<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Services\Contact\ContactService;
use App\Services\SeoService;
use Inertia\Inertia;

class LandingPageController extends Controller
{
    /**
     * Display the home/landing page
     */
    public function index()
    {
        $seo = (new SeoService())->set([
            'title'       => 'Nomad Tax Calculator — Free Digital Nomad Tax Tool',
            'description' => 'Free tax calculator for digital nomads. Calculate income tax, compare countries, and find the most tax-friendly destinations. No signup needed.',
            'canonical'   => url('/'),
            'og_image'    => asset('images/og-home.png'),
            'schema'      => json_encode([
                '@context'        => 'https://schema.org',
                '@type'           => 'WebSite',
                'name'            => 'NomadTaxCalc',
                'url'             => url('/'),
                'description'     => 'Free tax calculator for digital nomads',
                'potentialAction' => [
                    '@type'       => 'SearchAction',
                    'target'      => url('/tax-calculator') . '?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ]),
        ])->get();

        $landingData = [
            'seo'          => $seo,
            'features'     => $this->getFeatures(),
            'destinations' => $this->getPopularDestinations(),
            'testimonials' => $this->getTestimonials(),
            'howItWorks'   => $this->getHowItWorks(),
        ];

        return Inertia::render('Landing/Index', $landingData);
    }

    /**
     * Display the Privacy Policy page (public, no auth required)
     */
    public function privacyPolicy()
    {
        $seo = (new SeoService())->set([
            'title'       => 'Privacy Policy — NomadTaxCalc',
            'description' => 'Read the NomadTaxCalc privacy policy. Learn how we handle your data, cookies, and advertising with Google AdSense.',
            'canonical'   => url('/privacy-policy'),
        ])->get();

        return Inertia::render(
            'PrivacyPolicy/Index',
            [
                'seo'        => $seo,
                'mailConfig' => [
                    'from' => config('mail.from.address'),
                ],
            ]
        );
    }

    /**
     * Display the About page.
     */
    public function about()
    {
        $seo = (new SeoService())->set([
            'title'       => 'About NomadTaxCalc — Who We Are & Our Mission',
            'description' => 'NomadTaxCalc helps digital nomads calculate and compare taxes worldwide. Learn about our mission to simplify nomad taxation for freelancers and remote workers.',
            'canonical'   => url('/about'),
            'schema'      => json_encode([
                '@context'     => 'https://schema.org',
                '@type'        => 'Organization',
                'name'         => 'NomadTaxCalc',
                'url'          => url('/'),
                'description'  => 'Free tax calculator for digital nomads and remote workers',
                'contactPoint' => [
                    '@type'       => 'ContactPoint',
                    'contactType' => 'customer support',
                    'url'         => url('/contact'),
                ],
            ]),
        ])->get();

        return Inertia::render('About/Index', compact('seo'));
    }

    /**
     * Display the Contact page.
     */
    public function contact()
    {
        $seo = (new SeoService())->set([
            'title'       => 'Contact NomadTaxCalc — Get In Touch',
            'description' => 'Have a question about nomad taxes? Contact the NomadTaxCalc team. We respond within 24 hours.',
            'canonical'   => url('/contact'),
        ])->get();

        return Inertia::render('Contact/Index', compact('seo'));
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
                'title' => 'Scenario Comparison Tool',
                'description' => "What if I'd spent fewer days in Spain? Compare two travel scenarios side-by-side and instantly see the tax difference, residency impact, and how much you'd save.",
                'icon' => 'schedule',
            ],
            [
                'id' => 3,
                'title' => 'Business Structure Optimizer',
                'description' => 'Side-by-side tax burden comparison for Sole Proprietor vs. LLC vs. S-Corp structures, specifically modeled for the digital nomad income profile and travel pattern.',
                'icon' => 'business',
                'coming_soon' => true,
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
                'description' => 'Add your annual gross income, pick your currency, and choose your tax year. Select your country of citizenship and home state — we handle multi-currency conversions automatically.',
                'icon' => 'payments',
            ],
            [
                'id' => 2,
                'title' => 'Add Countries',
                'description' => 'Tell us where you lived this year and for how many days. We instantly track your 183-day threshold per country, show your residency status, and let you add custom local taxes.',
                'icon' => 'public',
            ],
            [
                'id' => 3,
                'title' => 'Get Your Strategy',
                'description' => 'See your total tax liability, effective rate, net income, applied treaties, FEIE eligibility, residency warnings, and smart optimization recommendations — all in one report.',
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
