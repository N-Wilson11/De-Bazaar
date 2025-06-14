<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Advertisement;
use Illuminate\Support\Facades\DB;

class EnableWearAndTearSeeder extends Seeder
{
    /**
     * Schakel slijtageberekening in voor bestaande verhuuradvertenties
     *
     * @return void
     */
    public function run()
    {
        // Zoek alle verhuuradvertenties
        $rentalAds = Advertisement::where('is_rental', true)->get();
        
        foreach ($rentalAds as $ad) {
            // Zorg ervoor dat ze allemaal een borg hebben
            if (!$ad->rental_deposit_amount || $ad->rental_deposit_amount == 0) {
                $ad->rental_deposit_amount = 50.00;
                $ad->rental_requires_deposit = true;
            }
            
            // Schakel slijtageberekening in
            $ad->rental_calculate_wear_and_tear = true;
            
            // Stel standaard slijtage-instellingen in
            $ad->rental_wear_and_tear_settings = [
                'base_percentage' => 2.0, // 2% per dag
                'condition_multipliers' => [
                    'excellent' => 0.0,   // Perfect staat, geen slijtage
                    'good' => 0.5,        // Goede staat, halve slijtage
                    'fair' => 1.0,        // Normale slijtage
                    'poor' => 2.0,        // Slechte staat, dubbele slijtage
                ]
            ];
            
            $ad->save();
            
            $this->command->info('Slijtageberekening ingeschakeld voor advertentie: ' . $ad->title);
        }
        
        $this->command->info('Slijtageberekening is ingeschakeld voor ' . $rentalAds->count() . ' verhuuradvertenties.');
    }
}
