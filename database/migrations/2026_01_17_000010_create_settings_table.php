<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // Setting key-value pair
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 50)->default('string'); // string, integer, boolean, array, json
            
            // Description & metadata
            $table->text('description')->nullable();
            $table->string('group', 50)->nullable()->index(); // app, mail, tax, etc.
            
            // Control
            $table->boolean('is_editable')->default(true);
            $table->timestamp('last_updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
