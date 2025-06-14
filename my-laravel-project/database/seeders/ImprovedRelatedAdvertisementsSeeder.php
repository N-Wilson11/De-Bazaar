<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Database\Seeder;

class ImprovedRelatedAdvertisementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Get all advertisements
        $advertisements = Advertisement::where('status', 'active')->get();
        
        if ($advertisements->count() < 5) {
            $this->command->info('Not enough advertisements found. Please run ImprovedAdvertisementSeeder first.');
            return;
        }
        
        // Create meaningful related advertisements
        $relatedByCategory = $this->createCategoryBasedRelations($advertisements);
        $relatedByUser = $this->createUserBasedRelations($advertisements);
        $relatedByComp = $this->createComplementaryRelations($advertisements);
        
        $this->command->info("Created {$relatedByCategory} category-based related advertisements.");
        $this->command->info("Created {$relatedByUser} user-based related advertisements.");
        $this->command->info("Created {$relatedByComp} complementary related advertisements.");
    }
    
    /**
     * Create related advertisements based on the same category
     * 
     * @param \Illuminate\Database\Eloquent\Collection $advertisements
     * @return int Number of relations created
     */
    private function createCategoryBasedRelations($advertisements): int
    {
        $relationsCount = 0;
        
        // Group advertisements by category
        $adsByCategory = $advertisements->groupBy('category');
        
        foreach ($adsByCategory as $category => $ads) {
            // Only process categories with at least 2 advertisements
            if ($ads->count() >= 2) {
                // For each advertisement, relate it to 1-3 others in the same category
                foreach ($ads as $ad) {
                    // Get other ads in the same category, excluding self
                    $otherAds = $ads->reject(function ($item) use ($ad) {
                        return $item->id === $ad->id;
                    });
                    
                    // If we have other ads, create relations
                    if ($otherAds->count() > 0) {
                        // Determine how many related ads to add (1-3)
                        $relatedCount = min($otherAds->count(), rand(1, 3));
                        $relatedAds = $otherAds->random($relatedCount);
                        
                        foreach ($relatedAds as $relatedAd) {
                            // Check if relation already exists
                            if (!$ad->relatedAdvertisements()->where('related_advertisement_id', $relatedAd->id)->exists()) {
                                $ad->relatedAdvertisements()->attach($relatedAd->id);
                                $relationsCount++;
                            }
                        }
                    }
                }
            }
        }
        
        return $relationsCount;
    }
    
    /**
     * Create related advertisements from the same user
     * 
     * @param \Illuminate\Database\Eloquent\Collection $advertisements
     * @return int Number of relations created
     */
    private function createUserBasedRelations($advertisements): int
    {
        $relationsCount = 0;
        
        // Group advertisements by user
        $adsByUser = $advertisements->groupBy('user_id');
        
        foreach ($adsByUser as $userId => $userAds) {
            // Only process if user has at least 2 advertisements
            if ($userAds->count() >= 2) {
                // For each advertisement, relate it to other ads from the same user
                foreach ($userAds as $ad) {
                    // Get other ads from the same user, excluding self
                    $otherUserAds = $userAds->reject(function ($item) use ($ad) {
                        return $item->id === $ad->id;
                    });
                    
                    // If we have other ads from same user, create relations
                    if ($otherUserAds->count() > 0) {
                        // Relate to all other ads from the same user
                        foreach ($otherUserAds as $otherAd) {
                            // Check if relation already exists
                            if (!$ad->relatedAdvertisements()->where('related_advertisement_id', $otherAd->id)->exists()) {
                                $ad->relatedAdvertisements()->attach($otherAd->id);
                                $relationsCount++;
                            }
                        }
                    }
                }
            }
        }
        
        return $relationsCount;
    }
    
    /**
     * Create complementary related advertisements (e.g. relate tools with materials)
     * 
     * @param \Illuminate\Database\Eloquent\Collection $advertisements
     * @return int Number of relations created
     */
    private function createComplementaryRelations($advertisements): int
    {
        $relationsCount = 0;
        
        // Define complementary categories
        $complementaryCategories = [
            'Gereedschap' => ['Bouwmaterialen', 'Tuin'],
            'Tuin' => ['Gereedschap', 'Planten'],
            'Sport' => ['Sport', 'Kleding'],
            'Elektronica' => ['Computers', 'Telefoons'],
            'Meubels' => ['Woonaccessoires', 'Verlichting'],
        ];
        
        // Process each advertisement
        foreach ($advertisements as $ad) {
            // Check if this category has defined complementary categories
            if (isset($complementaryCategories[$ad->category])) {
                $compCategories = $complementaryCategories[$ad->category];
                
                // Find advertisements in complementary categories
                $compAds = $advertisements->filter(function ($item) use ($ad, $compCategories) {
                    return in_array($item->category, $compCategories) && $item->id !== $ad->id;
                });
                
                // If we have complementary ads, create relations
                if ($compAds->count() > 0) {
                    // Determine how many related ads to add (1-2)
                    $relatedCount = min($compAds->count(), rand(1, 2));
                    $relatedAds = $compAds->random($relatedCount);
                    
                    foreach ($relatedAds as $relatedAd) {
                        // Check if relation already exists
                        if (!$ad->relatedAdvertisements()->where('related_advertisement_id', $relatedAd->id)->exists()) {
                            $ad->relatedAdvertisements()->attach($relatedAd->id);
                            $relationsCount++;
                        }
                    }
                }
            }
        }
        
        return $relationsCount;
    }
}
