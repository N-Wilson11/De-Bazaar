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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('condition')->nullable(); // nieuw, gebruikt, etc.
            $table->string('category');
            $table->string('type')->default('sale'); // sale, auction, rental
            $table->string('status')->default('active'); // active, inactive, sold, rented
            $table->json('images')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_highlighted')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('views')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
