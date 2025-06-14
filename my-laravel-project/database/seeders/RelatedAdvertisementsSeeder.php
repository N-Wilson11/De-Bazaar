<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RelatedAdvertisementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verhuur advertenties om aan te koppelen
        $rentalAds = Advertisement::where('is_rental', true)
            ->where('status', 'active')
            ->limit(5)
            ->get();
            
        // Verkoop advertenties om aan te koppelen
        $saleAds = Advertisement::where('is_rental', false)
            ->where('status', 'active')
            ->where('purchase_status', 'available')
            ->limit(10)
            ->get();
            
        // Koppel verkoopadvertenties aan verhuuradvertenties
        foreach ($rentalAds as $rentalAd) {
            // Selecteer 2-3 willekeurige verkoopadvertenties voor elke verhuuradvertentie
            $relatedCount = rand(2, 3);
            $relatedSaleAds = $saleAds->random($relatedCount);
            
            foreach ($relatedSaleAds as $saleAd) {
                // Voorkom koppeling van dezelfde advertentie
                if ($rentalAd->id !== $saleAd->id) {
                    $rentalAd->relatedAdvertisements()->attach($saleAd->id);
                }
            }
        }
        
        $this->command->info('Gerelateerde advertenties zijn succesvol toegevoegd!');
    }
}
