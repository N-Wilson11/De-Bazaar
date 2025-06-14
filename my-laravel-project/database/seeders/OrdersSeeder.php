<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Advertisement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find a non-admin user or create one
        $user = User::where('user_type', 'normaal')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test Klant',
                'email' => 'klant@example.com',
                'password' => Hash::make('password'),
                'user_type' => 'normaal',
            ]);
        }
        
        // Find a seller with ads or create one
        $seller = User::where('user_type', 'particulier')->first();
        if (!$seller) {
            $seller = User::create([
                'name' => 'Test Verkoper',
                'email' => 'verkoper@example.com',
                'password' => Hash::make('password'),
                'user_type' => 'particulier',
            ]);
            
            // Create some advertisements
            for ($i = 1; $i <= 3; $i++) {
                Advertisement::create([
                    'user_id' => $seller->id,
                    'title' => "Testproduct $i",
                    'description' => "Dit is een testproduct $i",
                    'price' => 50 * $i,
                    'condition' => 'new',
                    'category' => 'elektronica',
                    'type' => 'sale',
                    'status' => 'active',
                    'purchase_status' => 'available',
                    'location' => 'Amsterdam',
                    'is_rental' => false,
                ]);
            }
        }
        
        // Create a test order
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 150.00,
            'status' => 'pending',
            'payment_method' => 'ideal',
            'shipping_address' => 'Teststraat 123, Amsterdam',
            'billing_address' => 'Teststraat 123, Amsterdam',
            'notes' => 'Dit is een testbestelling',
        ]);
        
        // Find an advertisement to add to the order
        $advertisement = Advertisement::where('user_id', $seller->id)->first();
        
        if ($advertisement) {
            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'advertisement_id' => $advertisement->id,
                'seller_id' => $seller->id,
                'quantity' => 1,
                'price' => $advertisement->price,
                'title' => $advertisement->title,
            ]);
            
            // Update advertisement to sold
            $advertisement->purchase_status = 'sold';
            $advertisement->save();
        }
        
        // Create a completed order
        $completedOrder = Order::create([
            'user_id' => $user->id,
            'total_amount' => 200.00,
            'status' => 'completed',
            'payment_method' => 'creditcard',
            'shipping_address' => 'Teststraat 123, Amsterdam',
            'billing_address' => 'Teststraat 123, Amsterdam',
            'notes' => 'Dit is een voltooide testbestelling',
        ]);
        
        // Find another advertisement to add to the completed order
        $advertisement2 = Advertisement::where('user_id', $seller->id)
            ->where('id', '!=', $advertisement ? $advertisement->id : 0)
            ->first();
            
        if ($advertisement2) {
            // Create order item
            OrderItem::create([
                'order_id' => $completedOrder->id,
                'advertisement_id' => $advertisement2->id,
                'seller_id' => $seller->id,
                'quantity' => 1,
                'price' => $advertisement2->price,
                'title' => $advertisement2->title,
            ]);
            
            // Update advertisement to sold
            $advertisement2->purchase_status = 'sold';
            $advertisement2->save();
        }
    }
}
