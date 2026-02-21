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
        Schema::table('user_calculation_countries', function (Blueprint $table) {
            $table->json('selected_tax_type_ids')->nullable()->after('tax_by_type');
            $table->json('tax_type_overrides')->nullable()->after('selected_tax_type_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_calculation_countries', function (Blueprint $table) {
            $table->dropColumn(['selected_tax_type_ids', 'tax_type_overrides']);
        });
    }
};
