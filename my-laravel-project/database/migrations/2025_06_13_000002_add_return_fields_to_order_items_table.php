<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnFieldsToOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('is_returned')->default(false)->after('rental_end_date');
            $table->dateTime('returned_at')->nullable()->after('is_returned');
            $table->string('return_photo')->nullable()->after('returned_at');
            $table->text('return_notes')->nullable()->after('return_photo');
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
            $table->dropColumn(['is_returned', 'returned_at', 'return_photo', 'return_notes']);
        });
    }
}
