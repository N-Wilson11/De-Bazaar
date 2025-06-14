<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Advertisement;
use App\Models\User;
use App\Models\Bid;
use Carbon\Carbon;

class TestBiddingSeeder extends Seeder
{
    /**
     * Seed testgegevens voor biedingsfunctionaliteit.
     * 
     * Deze seeder maakt een testadvertentie aan waarop geboden kan worden
     * en zet een aantal testbiedingen klaar.
     */
    public function run(): void
    {
        // Zoek gebruikers om biedingen te plaatsen
        $users = User::all();
        
        if ($users->count() < 3) {
            $this->command->error('Er zijn niet genoeg gebruikers om mee te testen. Maak eerst meer gebruikers aan.');
            return;
        }
          // Haal een eigenaar en verschillende bieders
        $owner = $users->firstWhere('user_type', 'particulier') ?? $users->first();
        $bidders = $users->where('id', '!=', $owner->id)->values();
        
        // Maak een advertentie aan die biedingen accepteert
        $advertisement = Advertisement::create([
            'user_id' => $owner->id,
            'title' => 'Vintage fiets - Met biedingen!',
            'description' => 'Mooie vintage fiets in goede staat. Geaccepteerde biedingen vanaf €75.',
            'price' => 100.00,
            'condition' => 'goed',
            'category' => 'sport',
            'location' => 'Amsterdam',
            'status' => 'active',
            'is_accepting_bids' => true,
            'min_bid_amount' => 75.00,
            'images' => []
        ]);
        
        $this->command->info('Advertentie aangemaakt: ' . $advertisement->title);
        
        // Maak enkele biedingen aan
        $now = Carbon::now();
        
        // Eerste bieder: Een bod dat onder het minimumbod ligt
        Bid::create([
            'user_id' => $bidders[0]->id,
            'advertisement_id' => $advertisement->id,
            'amount' => 70.00, // Dit is onder het minimumbod
            'message' => 'Ik wil deze fiets graag kopen voor €70.',
            'status' => 'rejected',
            'created_at' => $now->copy()->subDays(3),
            'updated_at' => $now->copy()->subDays(3),
        ]);
        
        // Tweede bieder: Een geaccepteerd bod
        Bid::create([
            'user_id' => $bidders[1]->id,
            'advertisement_id' => $advertisement->id,
            'amount' => 80.00,
            'message' => 'Is €80 akkoord?',
            'status' => 'pending',
            'expires_at' => $now->copy()->addDays(6),
            'created_at' => $now->copy()->subDays(1),
            'updated_at' => $now->copy()->subDays(1),
        ]);
        
        // Derde bieder: Een hoger bod
        Bid::create([
            'user_id' => $bidders[2]->id,
            'advertisement_id' => $advertisement->id,
            'amount' => 90.00,
            'message' => 'Ik bied €90 voor deze fiets.',
            'status' => 'pending',
            'expires_at' => $now->copy()->addDays(7),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        $this->command->info('3 biedingen zijn aangemaakt.');
    }
}
