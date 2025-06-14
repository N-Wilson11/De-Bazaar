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
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('reviewer_id')->nullable()->after('advertisement_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('review_type', ['advertisement', 'advertiser'])->default('advertisement')->after('reviewer_id');
            $table->string('title')->nullable()->after('rating');
            
            // Maak advertisement_id nullable omdat reviews voor adverteerders geen advertisement_id nodig hebben
            $table->foreignId('advertisement_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['reviewer_id', 'review_type', 'title']);
            $table->foreignId('advertisement_id')->nullable(false)->change();
        });
    }
};
