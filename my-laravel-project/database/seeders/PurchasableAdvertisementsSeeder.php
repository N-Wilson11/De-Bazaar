<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PurchasableAdvertisementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a seller if there's no particulier user
        $seller = User::where('user_type', 'particulier')->first();
        
        if (!$seller) {
            $seller = User::create([
                'name' => 'Verkoper Demo',
                'email' => 'verkoper@example.com',
                'password' => Hash::make('password'),
                'user_type' => 'particulier',
            ]);
        }
        
        // Create sample purchasable advertisements
        $products = [
            [
                'title' => 'Samsung Galaxy S21',
                'description' => 'Prachtige Samsung Galaxy S21 smartphone in nieuwstaat. Inclusief oplader en originele verpakking.',
                'price' => 499.99,
                'condition' => 'used',
                'category' => 'elektronica',
                'location' => 'Amsterdam',
            ],
            [
                'title' => 'IKEA Eettafel',
                'description' => 'IKEA eettafel in goede staat. Afmetingen: 180x90x75cm. Afhalen in Utrecht.',
                'price' => 75.00,
                'condition' => 'used',
                'category' => 'meubels',
                'location' => 'Utrecht',
            ],
            [
                'title' => 'Nike Air Max 90',
                'description' => 'Nieuwe Nike Air Max 90 schoenen, maat 43. Nooit gedragen, originele doos inclusief.',
                'price' => 129.50,
                'condition' => 'new',
                'category' => 'kleding',
                'location' => 'Rotterdam',
            ],
            [
                'title' => 'PlayStation 5',
                'description' => 'PlayStation 5 console met 2 controllers. 1 TB SSD opslag. In perfecte staat.',
                'price' => 450.00,
                'condition' => 'used',
                'category' => 'elektronica',
                'location' => 'Den Haag',
            ],
            [
                'title' => 'Mountainbike Specialized',
                'description' => 'Specialized mountainbike, 2 jaar oud maar in goede staat. 27 versnellingen, hydraulische schijfremmen.',
                'price' => 695.00,
                'condition' => 'used',
                'category' => 'sport',
                'location' => 'Eindhoven',
            ],
        ];
        
        foreach ($products as $product) {
            Advertisement::create([
                'user_id' => $seller->id,
                'title' => $product['title'],
                'description' => $product['description'],
                'price' => $product['price'],
                'condition' => $product['condition'],
                'category' => $product['category'],
                'type' => 'sale',
                'status' => 'active',
                'purchase_status' => Advertisement::PURCHASE_STATUS_AVAILABLE,
                'location' => $product['location'],
                'is_rental' => false,
                'is_highlighted' => false,
                'is_featured' => false,
                'images' => [],
            ]);
        }
    }
}
