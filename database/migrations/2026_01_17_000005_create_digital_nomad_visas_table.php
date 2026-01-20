<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_nomad_visas', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            
            // Visa details
            $table->string('visa_name', 100);
            $table->enum('visa_type', ['digital_nomad', 'remote_worker', 'temporary_residence', 'long_stay']);
            $table->integer('duration_months');
            $table->decimal('visa_cost', 10, 2)->nullable();
            
            // Income requirements
            $table->decimal('min_monthly_income', 10, 2)->nullable();
            $table->decimal('min_annual_income', 12, 2)->nullable();
            $table->string('income_currency', 3)->default('USD');
            $table->text('income_source_requirements')->nullable(); // e.g., "Remote work only, no local income"
            
            // Requirements
            $table->integer('min_stay_days')->default(30);
            $table->integer('max_stay_days')->nullable();
            $table->integer('processing_days')->nullable();
            $table->text('additional_requirements')->nullable();
            
            // Benefits
            $table->boolean('can_work_locally')->default(false);
            $table->boolean('can_bring_family')->default(false);
            $table->text('benefits_description')->nullable();
            
            // Metadata
            $table->year('available_from')->default(2026);
            $table->boolean('is_active')->default(true)->index();
            $table->string('official_url', 255)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['country_id', 'is_active']);
            $table->index(['visa_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_nomad_visas');
    }
};
