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
        // Call seeders in the correct order
        $this->call([
            // 1. Create basic users and default theme
            UserTypesSeeder::class,
            CompanyThemeSeeder::class,
            
            // 2. Create sample companies for business users
            SampleCompanySeeder::class,
            
            // 3. Create realistic advertisements
            ImprovedAdvertisementSeeder::class,
            
            // 4. Create page components for company landing pages
            PageComponentSeeder::class,
            
            // 5. Create related advertisements with meaningful connections
            ImprovedRelatedAdvertisementsSeeder::class,
        ]);
        
        $this->command->info('Database seeded successfully with realistic data!');
    }
}
