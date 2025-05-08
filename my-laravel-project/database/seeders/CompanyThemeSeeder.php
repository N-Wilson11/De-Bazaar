<?php

namespace Database\Seeders;

use App\Models\CompanyTheme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Eerst controleren of er al thema's bestaan om duplicaten te voorkomen
        if (DB::table('company_themes')->count() > 0) {
            $this->command->info('Company themes already exist! Skipping...');
            return;
        }

        // Array van thema's die we willen aanmaken
        $themes = [
            [
                'company_id' => 'default',
                'name' => 'De Bazaar',
                'logo_path' => null,
                'favicon_path' => '/favicon.ico',
                'primary_color' => '#4a90e2',
                'secondary_color' => '#f5a623',
                'accent_color' => '#50e3c2',
                'text_color' => '#333333',
                'background_color' => '#ffffff',
                'footer_text' => '© ' . date('Y') . ' De Bazaar. All rights reserved.',
                'is_active' => true,
            ]
        ];

        // Thema's toevoegen aan de database
        foreach ($themes as $themeData) {
            CompanyTheme::create($themeData);
        }

        $this->command->info('Company themes seeded successfully!');
    }
}