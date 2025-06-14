<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Advertisement;
use App\Models\User;
use Carbon\Carbon;

class RentalTestSeeder extends Seeder
{
    /**
     * Seed test data voor de huurfunctionaliteit
     *
     * @return void
     */
    public function run()
    {
        // Maak een test gebruiker aan als die er nog niet is
        $seller = User::firstOrCreate(
            ['email' => 'verhuurder@test.com'],
            [
                'name' => 'Test Verhuurder',
                'email' => 'verhuurder@test.com',
                'password' => bcrypt('password'),
                'user_type' => 'seller',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        $renter = User::firstOrCreate(
            ['email' => 'huurder@test.com'],
            [
                'name' => 'Test Huurder',
                'email' => 'huurder@test.com',
                'password' => bcrypt('password'),
                'user_type' => 'buyer',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Maak een huradvertentie aan met slijtageberekening
        $advertisement = Advertisement::create([
            'user_id' => $seller->id,
            'title' => 'Boormachine - Test Huren met Slijtage',
            'description' => 'Dit is een testadvertentie voor het huren van een boormachine met slijtageberekening.',
            'price' => 50.00, // verkoopprijs
            'condition' => 'new',
            'category' => 'tools',
            'type' => 'physical',
            'status' => 'active',
            'purchase_status' => 'available',
            'location' => 'Amsterdam',
            'is_highlighted' => false,
            'is_featured' => false,
            'expires_at' => Carbon::now()->addDays(30),
            
            // Huurspecifieke velden
            'is_rental' => true,
            'rental_price_day' => 5.00,
            'rental_price_week' => 25.00,
            'rental_price_month' => 80.00,
            'minimum_rental_days' => 1,
            'rental_availability' => [],
            'rental_booked_dates' => [],
            'rental_conditions' => 'Alleen af te halen op werkdagen tussen 9:00 en 17:00.',
            'rental_requires_deposit' => true,
            'rental_deposit_amount' => 100.00,
            'rental_calculate_wear_and_tear' => true,
            'rental_wear_and_tear_settings' => [
                'base_percentage' => 2.0, // 2% van de borg per dag
                'condition_multipliers' => [
                    'excellent' => 0.0,   // Perfect staat, geen slijtage
                    'good' => 0.5,        // Goede staat, halve slijtage
                    'fair' => 1.0,        // Normale slijtage
                    'poor' => 2.0,        // Slechte staat, dubbele slijtage
                ]
            ],
            'rental_pickup_location' => 'Amsterdam',
            
            // Dummy plaatje
            'images' => ['placeholder.jpg'],
        ]);
        
        $this->command->info('Test gebruikers en huuradvertentie aangemaakt:');
        $this->command->info('- Verhuurder: verhuurder@test.com (wachtwoord: password)');
        $this->command->info('- Huurder: huurder@test.com (wachtwoord: password)');
        $this->command->info('- Advertentie ID: ' . $advertisement->id);
        $this->command->info('Test de slijtageberekening door het product te huren en terug te brengen.');
    }
}
