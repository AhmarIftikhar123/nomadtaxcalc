<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('name', 100)->unique();
            $table->string('iso_code', 2)->unique();
            $table->string('iso_code_3', 3)->unique();
            $table->string('currency_code', 3)->default('USD');
            $table->string('currency_symbol', 10)->default('$');
            
            // Tax System Information
            $table->boolean('has_progressive_tax')->default(true);
            $table->decimal('flat_tax_rate', 5, 2)->nullable();
            $table->boolean('taxes_worldwide_income')->default(false);
            $table->enum('tax_basis', ['worldwide', 'territorial', 'remittance'])->default('worldwide');

            
            // Digital Nomad Information
            $table->boolean('has_digital_nomad_visa')->default(false);
            $table->string('digital_nomad_visa_name', 100)->nullable();
            $table->decimal('min_income_for_visa', 10, 2)->nullable();
            $table->string('visa_income_period', 20)->nullable();
            
            // Tax Residency Rules
            $table->integer('tax_residency_days')->default(183);
            $table->boolean('counts_arrival_day')->default(true);
            $table->boolean('counts_departure_day')->default(false);
            $table->boolean('considers_center_of_vital_interests')->default(false);
            
            // Additional Information
            $table->text('tax_system_notes')->nullable();
            $table->text('visa_requirements')->nullable();
            $table->decimal('avg_monthly_cost_of_living', 10, 2)->nullable();
            $table->string('official_tax_authority_url', 255)->nullable();
            
            // SEO & Content
            $table->string('slug', 120)->unique();
            $table->text('meta_description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('popularity_rank')->default(999);
            
            // Status
            $table->boolean('is_active')->default(true)->index();
            $table->year('data_last_updated')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
 