<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            // Verhuur-specifieke velden
            $table->boolean('is_rental')->default(false);
            $table->decimal('rental_price_day', 10, 2)->nullable();
            $table->decimal('rental_price_week', 10, 2)->nullable();
            $table->decimal('rental_price_month', 10, 2)->nullable();
            $table->integer('minimum_rental_days')->nullable();
            $table->json('rental_availability')->nullable(); // JSON array van datums waarop item beschikbaar is
            $table->json('rental_booked_dates')->nullable(); // JSON array van datums waarop item verhuurd is
            $table->text('rental_conditions')->nullable(); // Voorwaarden voor verhuur
            $table->boolean('rental_requires_deposit')->default(false);
            $table->decimal('rental_deposit_amount', 10, 2)->nullable();
            $table->string('rental_pickup_location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn([
                'is_rental',
                'rental_price_day',
                'rental_price_week',
                'rental_price_month',
                'minimum_rental_days',
                'rental_availability',
                'rental_booked_dates',
                'rental_conditions',
                'rental_requires_deposit',
                'rental_deposit_amount',
                'rental_pickup_location'
            ]);
        });
    }
};
