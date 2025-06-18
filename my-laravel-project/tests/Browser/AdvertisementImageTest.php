<?php

namespace Tests\Browser;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class AdvertisementImageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // Make sure the public directory exists for advertisements
        if (!file_exists(public_path('images/advertisements'))) {
            mkdir(public_path('images/advertisements'), 0755, true);
        }
        
        // Create a test image
        copy(
            public_path('images/no-image.png'), 
            public_path('images/advertisements/test-image.jpg')
        );
    }

    /**
     * Test that advertisement images are displayed correctly.
     *
     * @return void
     */
    public function testAdvertisementImageDisplay()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create an advertisement with known images
        $advertisement = Advertisement::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Advertisement with Images',
            'images' => ['test-image.jpg'],
        ]);

        $this->browse(function (Browser $browser) use ($advertisement) {
            $browser->visit('/advertisements/' . $advertisement->id)
                    ->assertPresent('.advertisement-images img')
                    ->screenshot('advertisement-with-image');
        });
    }

    /**
     * Test that multiple advertisement images are displayed correctly.
     *
     * @return void
     */
    public function testMultipleAdvertisementImages()
    {
        // Create another test image
        copy(
            public_path('images/no-image.png'), 
            public_path('images/advertisements/test-image2.jpg')
        );
        
        // Create a user
        $user = User::factory()->create();
        
        // Create an advertisement with multiple images
        $advertisement = Advertisement::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Advertisement with Multiple Images',
            'images' => ['test-image.jpg', 'test-image2.jpg'],
        ]);

        $this->browse(function (Browser $browser) use ($advertisement) {
            $browser->visit('/advertisements/' . $advertisement->id)
                    ->assertPresent('.carousel-item img')
                    ->assertSeeIn('.carousel-item.active img', '')
                    ->screenshot('advertisement-with-multiple-images');
        });
    }
    
    /**
     * Test that the getAllImageUrls method works correctly.
     *
     * @return void
     */
    public function testGetAllImageUrls()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create an advertisement with images
        $advertisement = Advertisement::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Advertisement',
            'images' => ['test-image.jpg'],
        ]);
        
        // Get the image URLs
        $imageUrls = $advertisement->getAllImageUrls();
        
        // Assert that the URL contains the expected path
        $this->assertStringContainsString('images/advertisements/test-image.jpg', $imageUrls[0]);
    }
    
    /**
     * Clean up the test environment.
     *
     * @return void
     */
    public function tearDown(): void
    {
        // Remove test images
        @unlink(public_path('images/advertisements/test-image.jpg'));
        @unlink(public_path('images/advertisements/test-image2.jpg'));
        
        parent::tearDown();
    }
}
