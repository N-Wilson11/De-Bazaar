<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Advertisement;
use App\Models\User;
use Carbon\Carbon;

class AdvertisementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Eerst zorgen we ervoor dat er gebruikers zijn
        if (User::count() === 0) {
            $this->command->info('Er zijn geen gebruikers. Maak eerst gebruikers aan.');
            return;
        }

        // Gebruikers ophalen die geen admin zijn (alleen normale gebruikers mogen advertenties plaatsen)
        $users = User::whereIn('user_type', ['particulier', 'zakelijk', 'normaal'])->get();
        
        if ($users->isEmpty()) {
            $this->command->info('Er zijn geen geschikte gebruikers (particulier, zakelijk of normaal) om advertenties aan te maken.');
            return;
        }

        // Normale advertenties aanmaken
        $this->createRegularAdvertisements($users);
        
        // Huuradvertenties aanmaken
        $this->createRentalAdvertisements($users);
        
        $this->command->info('Advertenties zijn succesvol aangemaakt!');
    }
    
    /**
     * Maak normale advertenties aan
     */
    private function createRegularAdvertisements($users)
    {
        $categories = ['Elektronica', 'Meubels', 'Kleding', 'Auto\'s', 'Boeken', 'Sport', 'Speelgoed'];
        $conditions = ['nieuw', 'als nieuw', 'goed', 'gebruikt', 'met gebreken'];
        
        // 10 normale advertenties aanmaken
        for ($i = 1; $i <= 10; $i++) {
            $user = $users->random();
            
            Advertisement::create([
                'user_id' => $user->id,
                'title' => 'Normale advertentie ' . $i,
                'description' => 'Dit is een voorbeeldadvertentie met nummer ' . $i . '. Deze advertentie bevat een uitgebreide beschrijving van het product.',
                'price' => rand(10, 1000),
                'condition' => $conditions[array_rand($conditions)],
                'category' => $categories[array_rand($categories)],
                'type' => 'normaal',
                'status' => 'active',
                'images' => json_encode(['placeholder1.jpg', 'placeholder2.jpg']),
                'location' => 'Amsterdam',
                'is_highlighted' => rand(0, 1) === 1,
                'is_featured' => rand(0, 10) < 2, // 20% kans
                'is_rental' => false,
                'expires_at' => Carbon::now()->addDays(30),
            ]);
        }
    }
    
    /**
     * Maak huuradvertenties aan
     */
    private function createRentalAdvertisements($users)
    {
        $categories = ['Gereedschap', 'Auto\'s', 'Feestbenodigdheden', 'Camera\'s', 'Sportuitrusting', 'Vakantiehuizen'];
        $conditions = ['nieuw', 'als nieuw', 'goed', 'gebruikt'];
        
        // Beschikbaarheidsdata genereren (voor de komende 30 dagen)
        $today = Carbon::today();
        $availabilityDates = [];
        
        for ($i = 0; $i < 30; $i++) {
            $availabilityDates[] = $today->copy()->addDays($i)->format('Y-m-d');
        }
        
        // 8 huuradvertenties aanmaken
        for ($i = 1; $i <= 8; $i++) {
            $user = $users->random();
            
            // Willekeurige gereserveerde dagen genereren (1-5 dagen)
            $bookedDates = [];
            $numBookedDates = rand(1, 5);
            
            for ($j = 0; $j < $numBookedDates; $j++) {
                $randomDay = rand(0, 29);
                $bookedDates[] = $today->copy()->addDays($randomDay)->format('Y-m-d');
            }
            
            // Huurvoorwaarden
            $rentalConditions = [
                'Legitimatie verplicht',
                'Borg vereist',
                'Schade komt voor rekening van huurder',
                'Verzekering aanbevolen'
            ];
            
            Advertisement::create([
                'user_id' => $user->id,
                'title' => 'Huuradvertentie ' . $i,
                'description' => 'Dit is een voorbeeld huuradvertentie met nummer ' . $i . '. Dit product is beschikbaar voor verhuur.',
                'price' => rand(100, 2000), // Normale prijs (bij verkoop)
                'condition' => $conditions[array_rand($conditions)],
                'category' => $categories[array_rand($categories)],
                'type' => 'huur',
                'status' => 'active',
                'images' => json_encode(['rental_placeholder1.jpg', 'rental_placeholder2.jpg']),
                'location' => 'Rotterdam',
                'is_highlighted' => rand(0, 1) === 1,
                'is_featured' => rand(0, 10) < 3, // 30% kans
                'is_rental' => true,
                'rental_price_day' => rand(5, 50),
                'rental_price_week' => rand(25, 300),
                'rental_price_month' => rand(80, 1000),
                'minimum_rental_days' => rand(1, 3),
                'rental_availability' => json_encode($availabilityDates),
                'rental_booked_dates' => json_encode($bookedDates),
                'rental_conditions' => json_encode($rentalConditions),
                'rental_requires_deposit' => rand(0, 1) === 1,
                'expires_at' => Carbon::now()->addDays(60),
            ]);
        }
    }
}
