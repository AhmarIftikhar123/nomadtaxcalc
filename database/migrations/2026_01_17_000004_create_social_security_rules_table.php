<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_security_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->year('tax_year');
            $table->enum('contribution_type', ['employee', 'employer']);
            $table->string('fund_name', 100); // e.g., 'FICA - Social Security', 'NIC Class 1', 'Pension Insurance'
            $table->decimal('rate', 5, 2);     // Percentage rate
            $table->decimal('min_income', 15, 2)->default(0);     // Income floor
            $table->decimal('max_income', 15, 2)->nullable();     // Income cap (null = no cap)
            $table->decimal('annual_cap', 15, 2)->nullable();     // Max contribution amount (null = no cap)
            $table->char('currency_code', 3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['country_id', 'tax_year', 'contribution_type', 'is_active'], 'ss_rules_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_security_rules');
    }
};
