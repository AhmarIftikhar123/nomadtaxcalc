<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->year('tax_year');
            $table->enum('deduction_type', [
                'standard',              // Standard deduction (US)
                'personal_allowance',    // Personal allowance (UK, AU)
                'basic_relief',          // Basic relief / Grundfreibetrag (DE)
                'employment_income',     // Employment income deduction (JP)
                'social_contributions',  // Social contribution deduction (FR)
            ])->default('standard');
            $table->enum('filing_status', [
                'single',
                'married_joint',
                'married_separate',
                'head_of_household',
                'default',               // For countries without filing status distinctions
            ])->default('default');
            $table->decimal('amount', 15, 2);
            $table->boolean('is_percentage')->default(false); // true = percentage of income, false = fixed amount
            $table->decimal('phase_out_start', 15, 2)->nullable(); // Income where deduction starts reducing
            $table->decimal('phase_out_end', 15, 2)->nullable();   // Income where deduction fully phases out
            $table->char('currency_code', 3);                      // Currency the amount is denominated in
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['country_id', 'tax_year', 'is_active']);
            $table->unique(['country_id', 'tax_year', 'deduction_type', 'filing_status'], 'deduction_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};
