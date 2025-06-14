<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Create admin user (platformeigenaar)
         User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@debazaar.nl',
            'user_type' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // Create private user (particulier)
        User::factory()->create([
            'name' => 'Particulier User',
            'email' => 'particulier@example.com',
            'user_type' => 'particulier',
            'password' => Hash::make('password'),
        ]);        // Create business user (zakelijk)
        User::factory()->create([
            'name' => 'Zakelijk User',
            'email' => 'zakelijk@example.com',
            'user_type' => 'zakelijk',
            'password' => Hash::make('password'),
        ]);
        
        // Create normal user (normaal)
        User::factory()->create([
            'name' => 'Normale Gebruiker',
            'email' => 'normaal@example.com',
            'user_type' => 'normaal',
            'password' => Hash::make('password'),
        ]);
    }
}
