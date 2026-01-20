<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            
            // Content
            $table->string('title', 200);
            $table->string('slug', 250)->unique();
            $table->text('excerpt');
            $table->longText('content');
            
            // Author info
            $table->string('author', 100);
            $table->string('author_email', 100)->nullable();
            
            // Categories
            $table->string('category', 50)->index(); // tax-tips, visa-guide, cost-of-living, etc.
            $table->json('tags')->nullable(); // Array of tags
            
            // Country relation (optional, for country-specific posts)
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            
            // SEO
            $table->string('meta_description', 160)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('featured_image_url', 255)->nullable();
            
            // Status
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
