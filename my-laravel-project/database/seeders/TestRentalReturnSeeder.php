<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Advertisement;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TestRentalReturnSeeder extends Seeder
{
    /**
     * Maak een testgebruiker, advertentie, order en orderItem aan om het terugbrengen te testen
     *
     * @return void
     */
    public function run()
    {
        // 1. Maak een test gebruiker aan als die er nog niet is
        $seller = User::firstOrCreate(
            ['email' => 'verhuurder@test.com'],
            [
                'name' => 'Test Verhuurder',
                'email' => 'verhuurder@test.com',
                'password' => bcrypt('password'),
                'user_type' => 'particulier',
                'email_verified_at' => now(),
            ]
        );
        
        $renter = User::firstOrCreate(
            ['email' => 'huurder@test.com'],
            [
                'name' => 'Test Huurder',
                'email' => 'huurder@test.com',
                'password' => bcrypt('password'),
                'user_type' => 'particulier',
                'email_verified_at' => now(),
            ]
        );
        
        // 2. Maak een verhuuradvertentie aan
        $advertisement = Advertisement::firstOrCreate(
            ['title' => 'Boormachine - Test Huren met Slijtage'],
            [
                'user_id' => $seller->id,
                'title' => 'Boormachine - Test Huren met Slijtage',
                'description' => 'Dit is een testadvertentie voor het huren van een boormachine met slijtageberekening.',
                'price' => 50.00, // verkoopprijs
                'condition' => 'new',
                'category' => 'tools',
                'type' => 'rental',
                'status' => 'active',
                'purchase_status' => 'available',
                'location' => 'Amsterdam',
                'is_rental' => true,
                'rental_price_day' => 10.00,
                'rental_price_week' => 50.00,
                'minimum_rental_days' => 1,
                'rental_requires_deposit' => true,
                'rental_deposit_amount' => 100.00,
                'rental_calculate_wear_and_tear' => true,
                'rental_wear_and_tear_settings' => [
                    'base_percentage' => 2.0, // 2% van de borg per dag
                    'condition_multipliers' => [
                        'excellent' => 0.0,
                        'good' => 0.5,
                        'fair' => 1.0,
                        'poor' => 2.0,
                    ]
                ],
                'images' => ['placeholder.jpg'],
            ]
        );
        
        // 3. Maak een bestelling aan
        $order = Order::create([
            'user_id' => $renter->id,
            'total_amount' => 30.00, // 3 dagen huur
            'status' => 'completed',
            'payment_method' => 'cash',
            'notes' => 'Test huurbestelling',
        ]);
        
        // 4. Maak een orderItem aan voor de verhuur
        $rentalStartDate = Carbon::now()->subDays(5); // 5 dagen geleden
        $rentalEndDate = Carbon::now()->subDays(2);   // 2 dagen geleden
        
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'advertisement_id' => $advertisement->id,
            'seller_id' => $seller->id,
            'title' => $advertisement->title,
            'quantity' => 1,
            'price' => 30.00, // 3 dagen huur
            'deposit_amount' => $advertisement->rental_deposit_amount,
            'is_rental' => true,
            'rental_start_date' => $rentalStartDate->format('Y-m-d'),
            'rental_end_date' => $rentalEndDate->format('Y-m-d'),
            'is_returned' => true,
            'returned_at' => Carbon::now()->subDay(),
            'return_condition' => 'fair', // Normale slijtage
            'wear_and_tear_amount' => 6.00, // 3 dagen * 2% * 100 euro * 1.0 (fair conditie)
            'deposit_refunded_amount' => 94.00, // 100 - 6
            'return_notes' => 'Product is teruggebracht met normale gebruikssporen.',
            'return_photo' => 'return_photos/placeholder.jpg',
        ]);
        
        $this->command->info('Test data aangemaakt:');
        $this->command->info('- Verhuurder: verhuurder@test.com (wachtwoord: password)');
        $this->command->info('- Huurder: huurder@test.com (wachtwoord: password)');
        $this->command->info('- Advertentie ID: ' . $advertisement->id);
        $this->command->info('- Order ID: ' . $order->id);
        $this->command->info('- OrderItem ID: ' . $orderItem->id);
        $this->command->info('- Slijtage berekend: €' . $orderItem->wear_and_tear_amount);
        $this->command->info('- Borg terugbetaald: €' . $orderItem->deposit_refunded_amount);
    }
}
