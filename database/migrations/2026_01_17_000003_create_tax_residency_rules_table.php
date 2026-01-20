<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_residency_rules', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            
            // Rule type
            $table->enum('rule_type', [
                'days_in_country',
                'center_of_vital_interests',
                'permanent_home',
                'habitual_abode',
                'nationality'
            ]);
            
            // Details
            $table->integer('required_days')->nullable();
            $table->text('description');
            $table->text('exceptions')->nullable();
            
            // Metadata
            $table->year('effective_from')->default(2026);
            $table->year('effective_to')->nullable();
            $table->boolean('is_primary_rule')->default(false);
            $table->integer('rule_order')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['country_id', 'rule_type']);
            $table->index(['is_primary_rule']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_residency_rules');
    }
};
