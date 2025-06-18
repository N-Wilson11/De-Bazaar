<?php

namespace Tests\Browser;

use App\Models\Advertisement;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ImageTest extends DuskTestCase
{
    /**
     * Test that advertisement images are correctly displayed.
     *
     * @return void
     */
    public function testAdvertisementImages()
    {
        // Find an advertisement with images
        $ad = Advertisement::whereRaw("JSON_LENGTH(images) > 0")->first();
        
        if (!$ad || empty($ad->images)) {
            // Skip this test if no advertisements with images exist
            $this->markTestSkipped('No advertisements with images available for testing.');
            return;
        }
        
        $this->browse(function (Browser $browser) use ($ad) {
            // Visit the advertisement and take a screenshot to check for images
            $browser->visit('/advertisements/' . $ad->id)
                    ->waitForText($ad->title)
                    ->assertPresent('img')
                    ->screenshot('advertisement-image-display');
                    
            // We don't make assertions about the actual image content or sources
            // since we're just checking that images are there in some form
        });
    }
    
    /**
     * Test that home page shows advertisement thumbnails.
     *
     * @return void
     */
    public function testHomePageThumbnails()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertPresent('.card')
                    ->assertPresent('img')
                    ->screenshot('home-page-thumbnails');
        });
    }
}
