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
            
            $table->foreignId('country_id_1')->constrained('countries')->onDelete('cascade');
            $table->foreignId('country_id_2')->constrained('countries')->onDelete('cascade');
            
            // Treaty information
            $table->string('treaty_name', 200);
            $table->enum('treaty_type', ['income_tax', 'double_taxation', 'social_security', 'vat']);
            $table->year('effective_year')->default(2026);
            $table->text('key_benefits')->nullable();
            $table->string('treaty_document_url', 255)->nullable();
            
            // Status
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_updated_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Ensure we can easily find treaties between two countries
            $table->unique(['country_id_1', 'country_id_2', 'treaty_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_treaties');
    }
};
