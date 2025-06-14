<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRentalFieldsToOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('is_rental')->default(false)->after('price');
            $table->date('rental_start_date')->nullable()->after('is_rental');
            $table->date('rental_end_date')->nullable()->after('rental_start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['is_rental', 'rental_start_date', 'rental_end_date']);
        });
    }
}
