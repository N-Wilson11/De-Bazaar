<?php

namespace Tests\Browser;

use App\Models\Advertisement;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdvertisementTest extends DuskTestCase
{
    /**
     * Test that an advertisement's details page loads correctly.
     *
     * @return void
     */
    public function testAdvertisementDetailsPage()
    {
        // First check if there's at least one advertisement in the database
        $ad = Advertisement::first();
        
        if (!$ad) {
            // Skip this test if no advertisements exist
            $this->markTestSkipped('No advertisements available for testing.');
            return;
        }
        
        $this->browse(function (Browser $browser) use ($ad) {
            $browser->visit('/advertisements/' . $ad->id)
                    ->assertPresent('.card-body')
                    ->assertPresent('body') // At minimum, the page should load
                    ->screenshot('advertisement-detail');
        });
    }
    
    /**
     * Test that advertisement images are displayed.
     *
     * @return void
     */
    public function testImageDisplay()
    {
        // Find an advertisement with images
        $ad = Advertisement::whereNotNull('images')->first();
        
        if (!$ad || empty($ad->images)) {
            // Skip this test if no advertisements with images exist
            $this->markTestSkipped('No advertisements with images available for testing.');
            return;
        }
        
        $this->browse(function (Browser $browser) use ($ad) {
            $browser->visit('/advertisements/' . $ad->id)
                    ->assertPresent('img')  // Check that at least one image is present
                    ->screenshot('advertisement-images');
        });
    }

    /**
     * Test that advertisement details are displayed.
     *
     * @return void
     */
    public function testAdvertisementDetailsContent()
    {
        // Find an advertisement in the database
        $ad = Advertisement::first();
        
        if (!$ad) {
            // Skip this test if no advertisements exist
            $this->markTestSkipped('No advertisements available for testing.');
            return;
        }
          $this->browse(function (Browser $browser) use ($ad) {
            $browser->visit('/advertisements/' . $ad->id)
                    ->assertPresent('.card')
                    ->screenshot('advertisement-details-content');
        });
    }
}
