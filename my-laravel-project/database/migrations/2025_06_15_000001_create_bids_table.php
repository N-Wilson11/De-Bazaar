<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('advertisement_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, accepted, rejected, expired
            $table->text('message')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            
            // Voeg een unieke index toe om te voorkomen dat een gebruiker meerdere biedingen op dezelfde advertentie plaatst
            $table->unique(['user_id', 'advertisement_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bids');
    }
}
