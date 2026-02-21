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
            $table->uuid('session_uuid')->unique();

            // Step tracking
            $table->tinyInteger('step_reached')->default(1);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('completed_calculation')->default(false);

            // Calculation inputs
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('domicile_state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->decimal('gross_income', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->year('tax_year')->default(2026);
            $table->string('citizenship_country_code', 2)->nullable();
            $table->json('additional_inputs')->nullable();
            $table->json('included_tax_types')->nullable(); // e.g. ["income_tax", "social_security"]

            // Results
            $table->decimal('taxable_income', 12, 2)->nullable();
            $table->decimal('total_tax', 12, 2)->nullable();
            $table->decimal('net_income', 12, 2)->nullable();
            $table->decimal('effective_tax_rate', 5, 2)->nullable();

            // Detailed result breakdowns (JSON)
            $table->json('tax_breakdown')->nullable();
            $table->json('residency_warnings')->nullable();
            $table->json('treaty_applied')->nullable();
            $table->json('feie_result')->nullable();

            // Metadata
            $table->string('device_type', 50)->nullable();
            $table->string('referrer', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['country_id', 'created_at', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_calculations');
    }
};
