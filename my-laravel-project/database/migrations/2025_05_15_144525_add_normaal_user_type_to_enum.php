<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to modify the column directly to add 'normaal' type
        DB::statement("ALTER TABLE users MODIFY user_type ENUM('particulier', 'zakelijk', 'admin', 'normaal') DEFAULT 'particulier'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, we need to update any 'normaal' users to prevent errors
        // Convert any 'normaal' users to 'particulier' to avoid data truncation errors
        User::where('user_type', 'normaal')->update(['user_type' => 'particulier']);

        // Now we can safely revert the column definition
        DB::statement("ALTER TABLE users MODIFY user_type ENUM('particulier', 'zakelijk', 'admin') DEFAULT 'particulier'");
    }
};
