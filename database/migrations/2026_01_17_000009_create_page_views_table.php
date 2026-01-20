<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            
            // Page tracking
            $table->string('page_path', 255)->index();
            $table->string('page_title', 255)->nullable();
            
            // Visitor info
            $table->uuid('visitor_uuid')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            
            // Referrer info
            $table->string('referrer', 255)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('utm_content', 100)->nullable();
            
            // Geo info
            $table->string('country_code', 2)->nullable()->index();
            
            // Session
            $table->string('session_id', 100)->nullable();
            
            $table->timestamps();
            
            // Indexes for analytics
            $table->index(['page_path', 'created_at']);
            $table->index(['created_at']);
            $table->index(['utm_source', 'utm_medium']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
