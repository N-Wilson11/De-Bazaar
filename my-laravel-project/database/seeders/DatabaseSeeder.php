<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the UserTypesSeeder to create our admin, particulier, and zakelijk users
        $this->call([
            UserTypesSeeder::class,
            CompanyThemeSeeder::class,
            AdvertisementSeeder::class,
            PurchasableAdvertisementsSeeder::class,
        ]);
    }
}
