<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('user_calculations', 'tax_year')) {
            Schema::table('user_calculations', function (Blueprint $table) {
                $table->year('tax_year')->default(2026)->after('currency');
            });
        }
    }

    public function down(): void
    {
        Schema::table('user_calculations', function (Blueprint $table) {
            $table->dropColumn('tax_year');
        });
    }
};
