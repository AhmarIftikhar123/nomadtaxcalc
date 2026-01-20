<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Portugal Tax Guide 2026: NHR Benefits Explained',
                'slug' => 'portugal-tax-guide-2026-nhr-benefits',
                'excerpt' => 'Comprehensive guide to Portugal\'s Non-Habitual Resident (NHR) program offering up to 10 years of tax benefits.',
                'content' => 'Portugal\'s NHR program is one of Europe\'s most attractive tax incentives. Get details on eligibility, benefits, and how to apply...',
                'author' => 'Tax Team',
                'author_email' => 'info@taxtool.com',
                'category' => 'tax-tips',
                'tags' => json_encode(['portugal', 'nhr', 'tax-residency', '2026']),
                'country_id' => 1,
                'meta_description' => 'Portugal NHR program guide 2026 - tax benefits, eligibility, application process',
                'meta_keywords' => 'Portugal, NHR, tax, residency',
                'featured_image_url' => '/images/portugal-nhr.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(30),
            ],
            [
                'title' => 'Spain Digital Nomad Visa: Complete Guide 2026',
                'slug' => 'spain-digital-nomad-visa-guide-2026',
                'excerpt' => 'Step-by-step guide to obtaining Spain\'s new Digital Nomad Visa with all requirements and benefits.',
                'content' => 'Spain launched its Digital Nomad Visa in 2023 to attract remote workers. Here\'s everything you need to know...',
                'author' => 'Visa Expert',
                'author_email' => 'visas@taxtool.com',
                'category' => 'visa-guide',
                'tags' => json_encode(['spain', 'visa', 'digital-nomad', 'remote-work']),
                'country_id' => 2,
                'meta_description' => 'Spain Digital Nomad Visa 2026 - requirements, costs, and application',
                'meta_keywords' => 'Spain, digital nomad visa, remote work',
                'featured_image_url' => '/images/spain-visa.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(20),
            ],
            [
                'title' => 'Cost of Living: Portugal vs Spain vs Thailand',
                'slug' => 'cost-of-living-comparison-2026',
                'excerpt' => 'Detailed comparison of living expenses in top digital nomad destinations.',
                'content' => 'Comparing your options? Check our breakdown of average expenses in Portugal, Spain, and Thailand...',
                'author' => 'Research Team',
                'author_email' => 'research@taxtool.com',
                'category' => 'cost-of-living',
                'tags' => json_encode(['cost-of-living', 'comparison', 'digital-nomad', 'budget']),
                'country_id' => null,
                'meta_description' => 'Cost of living comparison 2026 - Portugal vs Spain vs Thailand',
                'meta_keywords' => 'cost of living, digital nomad, budget',
                'featured_image_url' => '/images/cost-comparison.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(10),
            ],
        ];

        DB::table('blog_posts')->insert($posts);
    }
}
