<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyTheme;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Get the business users
        $businessUsers = User::where('user_type', 'zakelijk')->get();
        
        if ($businessUsers->isEmpty()) {
            $this->command->info('No business users found. Please run UserTypesSeeder first.');
            return;
        }
        
        // Sample companies with realistic data
        $sampleCompanies = [
            [
                'name' => 'De Meubelzaak',
                'description' => 'Specialisten in hoogwaardige meubels voor uw huis en tuin. Wij bieden zowel nieuwe als vintage meubelen van topkwaliteit.',
                'categories' => ['Meubels', 'Woonaccessoires'],
                'website' => 'https://www.demeubelzaak.nl',
                'primary_color' => '#8B4513',
                'secondary_color' => '#D2B48C',
                'accent_color' => '#CD853F',
            ],
            [
                'name' => 'TechTrends',
                'description' => 'De nieuwste elektronica en gadgets voor scherpe prijzen. Van smartphones tot laptops en gaming accessoires.',
                'categories' => ['Elektronica', 'Computers', 'Telefoons'],
                'website' => 'https://www.techtrends.nl',
                'primary_color' => '#1E90FF',
                'secondary_color' => '#87CEFA',
                'accent_color' => '#4169E1',
            ],
            [
                'name' => 'Groene Vingers Tuincentrum',
                'description' => 'Alles voor uw tuin, van planten en zaden tot tuingereedschap en meubels. Deskundig advies voor elke tuinliefhebber.',
                'categories' => ['Tuin', 'Planten', 'Gereedschap'],
                'website' => 'https://www.groenevingerstuincentrum.nl',
                'primary_color' => '#228B22',
                'secondary_color' => '#32CD32',
                'accent_color' => '#3CB371',
            ],
            [
                'name' => 'Vintage Verzamelingen',
                'description' => 'Antiek, vintage items en verzamelobjecten. Unieke stukken met een verhaal voor verzamelaars en liefhebbers.',
                'categories' => ['Antiek', 'Verzamelobjecten', 'Kunst'],
                'website' => 'https://www.vintageverzamelingen.nl',
                'primary_color' => '#800000',
                'secondary_color' => '#B22222',
                'accent_color' => '#CD5C5C',
            ],
            [
                'name' => 'De Tool Verhuur',
                'description' => 'Professioneel gereedschap te huur voor elke klus. Van boormachines en slijptollen tot hoogwerkers en grondverzetmachines.',
                'categories' => ['Gereedschap', 'Bouwmaterialen'],
                'website' => 'https://www.detoolsverhuur.nl',
                'primary_color' => '#FF8C00',
                'secondary_color' => '#FFD700',
                'accent_color' => '#FFA500',
            ],
        ];
        
        foreach ($sampleCompanies as $index => $companyData) {
            // If we have enough business users, assign different users to companies
            // Otherwise, assign the first business user to all companies
            $user = ($index < $businessUsers->count()) ? $businessUsers[$index] : $businessUsers[0];
            
            // Create company
            $company = new Company([
                'name' => $companyData['name'],
                'slug' => Str::slug($companyData['name']),
                'email' => $user->email,
                'description' => $companyData['description'],
                'website' => $companyData['website'],
                'landing_url' => Str::slug($companyData['name']),
                'is_active' => true,
                'phone' => '0' . rand(610000000, 699999999),
                'address' => 'Voorbeeldweg ' . rand(1, 100),
                'postal_code' => rand(1000, 9999) . ' ' . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2)),
                'city' => $this->getRandomDutchCity(),
                'country' => 'Nederland',
            ]);
            
            $company->save();
            
            // Update user with company_id
            $user->company_id = $company->id;
            $user->save();
            
            // Create company theme
            $theme = new CompanyTheme([
                'company_id' => $company->id,
                'name' => $company->name,
                'primary_color' => $companyData['primary_color'],
                'secondary_color' => $companyData['secondary_color'],
                'accent_color' => $companyData['accent_color'],
                'text_color' => '#333333',
                'background_color' => '#ffffff',
                'footer_text' => '© ' . date('Y') . ' ' . $company->name . '. Alle rechten voorbehouden.',
                'is_active' => true,
            ]);
            
            $theme->save();
            
            $this->command->info("Created company: {$company->name}");
        }
    }
    
    /**
     * Get a random Dutch city name
     *
     * @return string
     */
    private function getRandomDutchCity(): string
    {
        $cities = [
            'Amsterdam', 'Rotterdam', 'Den Haag', 'Utrecht', 'Eindhoven', 
            'Tilburg', 'Groningen', 'Almere', 'Breda', 'Nijmegen',
            'Enschede', 'Haarlem', 'Arnhem', 'Amersfoort', 'Zaanstad',
            'Apeldoorn', 'Zwolle', 'Delft', 'Alkmaar', 'Dordrecht'
        ];
        
        return $cities[array_rand($cities)];
    }
}
