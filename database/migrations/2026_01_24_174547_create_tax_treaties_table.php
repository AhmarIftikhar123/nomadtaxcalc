<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_treaties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_a_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('country_b_id')->constrained('countries')->onDelete('cascade');
            $table->enum('treaty_type', ['credit', 'exemption', 'partial', 'totalization'])->default('credit');
            $table->year('applicable_tax_year')->default(2026);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['country_a_id', 'country_b_id', 'is_active']);
            // Shorter unique constraint name due to MySQL 64-char limit
            $table->unique(['country_a_id', 'country_b_id', 'applicable_tax_year'], 'treaty_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_treaties');
    }
};
