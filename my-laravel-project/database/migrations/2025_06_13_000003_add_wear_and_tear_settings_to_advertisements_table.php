<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWearAndTearSettingsToAdvertisementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->boolean('rental_calculate_wear_and_tear')->default(false)->after('rental_deposit_amount');
            $table->json('rental_wear_and_tear_settings')->nullable()->after('rental_calculate_wear_and_tear');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn([
                'rental_calculate_wear_and_tear',
                'rental_wear_and_tear_settings'
            ]);
        });
    }
}
