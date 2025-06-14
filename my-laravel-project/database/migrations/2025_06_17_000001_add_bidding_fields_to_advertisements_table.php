<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBiddingFieldsToAdvertisementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->boolean('is_accepting_bids')->default(false)->after('is_featured');
            $table->decimal('min_bid_amount', 10, 2)->nullable()->after('is_accepting_bids');
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
            $table->dropColumn('is_accepting_bids');
            $table->dropColumn('min_bid_amount');
        });
    }
}
