<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_exchange_rates', function (Blueprint $table) {
            $table->id();
            
            // Currency pair
            $table->string('from_currency', 3)->index();
            $table->string('to_currency', 3)->index();
            
            // Rate data
            $table->decimal('exchange_rate', 12, 8);
            $table->date('rate_date')->index();
            $table->string('source', 50)->nullable(); // e.g., 'ECB', 'OpenExchangeRates', 'XE'
            
            // Metadata
            $table->decimal('bid_price', 12, 8)->nullable();
            $table->decimal('ask_price', 12, 8)->nullable();
            $table->timestamp('last_updated_at')->nullable();
            
            $table->timestamps();
            
            // Unique rate per currency pair per date
            $table->unique(['from_currency', 'to_currency', 'rate_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_exchange_rates');
    }
};
