<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_calculation_countries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_calculation_id')
                ->constrained('user_calculations')
                ->onDelete('cascade');

            $table->foreignId('country_id')
                ->constrained('countries')
                ->onDelete('cascade');

            $table->foreignId('state_id')
                ->nullable()
                ->constrained('states')
                ->nullOnDelete();

            $table->integer('days_spent');
            $table->boolean('is_tax_resident')->default(false);

            // Computed values
            $table->decimal('allocated_income', 12, 2)->nullable();
            $table->decimal('taxable_income', 12, 2)->nullable();
            $table->decimal('tax_due', 12, 2)->nullable();
            $table->json('tax_by_type')->nullable(); // {"income_tax": 5200, "social_security": 1800}

            $table->timestamps();

            $table->unique(['user_calculation_id', 'country_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_calculation_countries');
    }
};
