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
            $table->decimal('average_rating', 3, 2)->nullable()->default(null);
            $table->integer('review_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn(['average_rating', 'review_count']);
        });
    }
};
