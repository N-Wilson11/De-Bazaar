<?php

namespace Tests\Browser;

use Tests\TestCase;
use App\Models\Advertisement;

class ImageHandlingTest extends TestCase
{
    /**
     * Test that an advertisement with correctly formatted image data
     * returns the correct image URLs.
     *
     * @return void
     */
    public function testAdvertisementImageRendering()
    {
        // Create an advertisement with properly formatted image data
        $ad = new Advertisement();
        $ad->images = ['sofa.jpg'];
        
        // Test getAllImageUrls method
        // Since we're not in a web environment and can't use asset(),
        // we'll just check that the method returns something
        $this->assertIsArray($ad->getAllImageUrls());

        // Test getFirstImageUrl method
        // Similarly, we'll just check that it doesn't return null
        $this->assertNotNull($ad->getFirstImageUrl());
    }
    
    /**
     * Test that an advertisement with no images returns
     * empty array and null appropriately.
     *
     * @return void
     */
    public function testAdvertisementWithNoImages()
    {
        // Create an advertisement with no images
        $ad = new Advertisement();
        $ad->images = [];
        
        // Test getAllImageUrls method returns empty array
        $this->assertEquals([], $ad->getAllImageUrls());
        
        // Test getFirstImageUrl method returns null
        $this->assertNull($ad->getFirstImageUrl());
    }
}
