<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\PageComponent;
use Illuminate\Database\Seeder;

class PageComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Get all companies
        $companies = Company::all();
        
        if ($companies->isEmpty()) {
            $this->command->info('No companies found. Please run SampleCompanySeeder first.');
            return;
        }
        
        foreach ($companies as $company) {
            $this->createPageComponentsForCompany($company);
        }
    }
    
    /**
     * Create page components for a company
     *
     * @param Company $company
     */
    private function createPageComponentsForCompany(Company $company): void
    {
        // Clear existing components
        $company->pageComponents()->delete();
        
        // Create title component
        $titleComponent = new PageComponent([
            'company_id' => $company->id,
            'type' => 'title',
            'content' => "Welkom bij {$company->name}",
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $titleComponent->save();
        
        // Create text component with company description
        $textComponent = new PageComponent([
            'company_id' => $company->id,
            'type' => 'text',
            'content' => "<p>{$company->description}</p><p>Wij staan klaar om u te helpen met professioneel advies en de beste producten.</p>",
            'sort_order' => 2,
            'is_active' => true,
        ]);
        $textComponent->save();
        
        // Create featured ads component
        $featuredAdsComponent = new PageComponent([
            'company_id' => $company->id,
            'type' => 'featured_ads',
            'content' => '',
            'sort_order' => 3,
            'settings' => [
                'count' => 4,
                'category' => null
            ],
            'is_active' => true,
        ]);
        $featuredAdsComponent->save();
        
        // Create image component
        $imageComponent = new PageComponent([
            'company_id' => $company->id,
            'type' => 'image',
            'content' => '',
            'sort_order' => 4,
            'settings' => [
                'image_path' => $this->getRandomImageForCompany($company),
                'alt_text' => "Afbeelding van {$company->name}"
            ],
            'is_active' => true,
        ]);
        $imageComponent->save();
        
        $this->command->info("Created landing page components for: {$company->name}");
    }
    
    /**
     * Get a random image path appropriate for the company type
     *
     * @param Company $company
     * @return string
     */
    private function getRandomImageForCompany(Company $company): string
    {
        // In a real scenario, we'd have actual images in the public/images directory
        // For now, we'll return a placeholder path
        return 'images/placeholder.jpg';
        
        // Example of how this would work with real images:
        /*
        $images = [
            'De Meubelzaak' => ['images/furniture1.jpg', 'images/furniture2.jpg'],
            'TechTrends' => ['images/tech1.jpg', 'images/tech2.jpg'],
            'Groene Vingers Tuincentrum' => ['images/garden1.jpg', 'images/garden2.jpg'],
            'Vintage Verzamelingen' => ['images/vintage1.jpg', 'images/vintage2.jpg'],
            'De Tool Verhuur' => ['images/tools1.jpg', 'images/tools2.jpg'],
        ];
        
        if (isset($images[$company->name])) {
            return $images[$company->name][array_rand($images[$company->name])];
        }
        
        return 'images/placeholder.jpg';
        */
    }
}
