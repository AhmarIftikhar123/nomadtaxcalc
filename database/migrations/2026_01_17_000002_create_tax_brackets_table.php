<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_brackets', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            
            // Tax bracket information
            $table->decimal('min_income', 12, 2);
            $table->decimal('max_income', 12, 2)->nullable(); // NULL = no upper limit
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('deductible_amount', 12, 2)->default(0); // Base deduction or credits
            
            // Metadata
            $table->year('effective_year')->default(2026);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['country_id', 'effective_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_brackets');
    }
};
