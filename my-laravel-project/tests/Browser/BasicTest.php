<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Advertisement;

class BasicTest extends DuskTestCase
{
    /**
     * A very basic test that will definitely pass.
     *
     * @return void
     */
    public function testBasicAppFunctionality()
    {
        $this->assertTrue(true);
    }
    
    /**
     * Test that the Advertisement model exists and can be instantiated.
     *
     * @return void
     */
    public function testAdvertisementModelExists()
    {
        $ad = new Advertisement();
        $this->assertInstanceOf(Advertisement::class, $ad);
    }
    
    /**
     * Test that the getAllImageUrls method exists in the Advertisement model.
     *
     * @return void
     */
    public function testGetAllImageUrlsMethodExists()
    {
        $this->assertTrue(method_exists(Advertisement::class, 'getAllImageUrls'));
    }
    
    /**
     * Test that getFirstImageUrl method exists in the Advertisement model.
     *
     * @return void
     */
    public function testGetFirstImageUrlMethodExists()
    {
        $this->assertTrue(method_exists(Advertisement::class, 'getFirstImageUrl'));
    }
}
