<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature   = 'sitemap:generate';
    protected $description = 'Generate the XML sitemap for NomadTaxCalc';

    public function handle(): void
    {
        Sitemap::create()
            // Money pages — highest priority
            ->add(Url::create('/')
                ->setPriority(1.0)
                ->setChangeFrequency('weekly'))
            ->add(Url::create('/tax-calculator')
                ->setPriority(0.95)
                ->setChangeFrequency('monthly'))
            ->add(Url::create('/tax-calculator/compare')
                ->setPriority(0.90)
                ->setChangeFrequency('monthly'))

            // Trust / AdSense-required pages
            ->add(Url::create('/about')
                ->setPriority(0.70)
                ->setChangeFrequency('yearly'))
            ->add(Url::create('/contact')
                ->setPriority(0.60)
                ->setChangeFrequency('yearly'))
            ->add(Url::create('/privacy-policy')
                ->setPriority(0.50)
                ->setChangeFrequency('yearly'))

            // NOTE: Do NOT include /tax-calculator/shared/* or any auth routes
            ->writeToFile(public_path('sitemap.xml'));

        $this->info('✅ sitemap.xml generated at public/sitemap.xml');
    }
}
