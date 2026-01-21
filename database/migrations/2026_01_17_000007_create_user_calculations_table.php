<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_calculations', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_uuid')->unique(); // Track user sessions

            // Calculation inputs
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable(); // IPv4 or IPv6
            $table->decimal('gross_income', 12, 2);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->json('additional_inputs')->nullable(); // Extra parameters

            // Results
            $table->decimal('taxable_income', 12, 2);
            $table->decimal('total_tax', 12, 2);
            $table->decimal('net_income', 12, 2);
            $table->decimal('effective_tax_rate', 5, 2);
            // For US Citizen + FEIE TAX
            $table->string('citizenship_country_code', 2)->nullable();
            $table->boolean('is_us_citizen')->default(false);
            $table->integer('days_outside_home_country')->nullable();
            $table->boolean('feie_eligible')->default(false);
            $table->decimal('feie_excluded_income', 12, 2)->default(0);

            // Metadata
            $table->string('device_type', 50)->nullable(); // mobile, desktop, tablet
            $table->string('referrer', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['country_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_calculations');
    }
};
