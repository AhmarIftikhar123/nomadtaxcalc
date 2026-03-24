<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // states table moved to separate migration
        Schema::create('tax_brackets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('state_id')->nullable()->constrained('states')->onDelete('cascade');
            $table->foreignId('tax_type_id')->constrained('tax_types')->onDelete('cascade');
            $table->year('tax_year'); // e.g. 2026
            $table->decimal('min_income', 15, 2);
            $table->decimal('max_income', 15, 2)->nullable(); // NULL for top bracket
            $table->decimal('rate', 5, 2); // percentage (e.g. 24.00 for 24%)
            $table->boolean('has_cap')->default(false);
            $table->decimal('annual_cap', 15, 2)->nullable(); // Max amount collectible
            $table->char('currency_code', 3)->nullable(); // Currency brackets are denominated in
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['country_id', 'tax_type_id', 'tax_year', 'is_active']);
            $table->unique(['country_id', 'state_id', 'tax_type_id', 'tax_year', 'min_income'], 'tax_bracket_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_brackets');
    }
};
