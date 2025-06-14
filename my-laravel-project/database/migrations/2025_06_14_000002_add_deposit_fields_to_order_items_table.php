<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepositFieldsToOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('deposit_amount', 10, 2)->nullable()->after('price');
            $table->decimal('deposit_refunded_amount', 10, 2)->nullable()->after('wear_and_tear_amount');
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
            $table->dropColumn(['deposit_amount', 'deposit_refunded_amount']);
        });
    }
}
