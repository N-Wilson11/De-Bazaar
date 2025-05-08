<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_themes', function (Blueprint $table) {
            $table->id();
            $table->string('company_id')->unique()->comment('Identifier for the company');
            $table->string('name')->default('My Company');
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('primary_color')->default('#4a90e2');
            $table->string('secondary_color')->default('#f5a623');
            $table->string('accent_color')->default('#50e3c2');
            $table->string('text_color')->default('#333333');
            $table->string('background_color')->default('#ffffff');
            $table->string('custom_css_path')->nullable();
            $table->string('custom_js_path')->nullable();
            $table->text('footer_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_themes');
    }
};