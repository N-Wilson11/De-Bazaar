<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Advertisement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdvertisementImageDebugTest extends DuskTestCase
{
    /**
     * Test to debug advertisement image display.
     *
     * @return void
     */
    public function testDebugAdvertisementImagePath()
    {
        // Skip database migrations and use existing data
        $this->browse(function (Browser $browser) {
            // Find an advertisement from the database
            $advertisement = DB::table('advertisements')->first();
            
            if (!$advertisement) {
                $this->markTestSkipped('No advertisements found in the database');
                return;
            }
            
            // Get the ID
            $id = $advertisement->id;
            
            // Visit the advertisement page
            $browser->visit("/advertisements/{$id}")
                ->pause(1000)
                ->screenshot("advertisement-{$id}");
                
            // Write image debug info to a file
            $images = json_decode($advertisement->images, true);
            $debugInfo = [
                'advertisement_id' => $id,
                'images_in_db' => $images,
                'image_files_exist' => []
            ];
            
            if (is_array($images)) {
                foreach ($images as $img) {
                    // Check if file exists in public/storage/advertisements
                    $path1 = public_path("storage/advertisements/{$img}");
                    $exists1 = File::exists($path1);
                    
                    // Check if file exists in public/images/advertisements
                    $path2 = public_path("images/advertisements/{$img}");
                    $exists2 = File::exists($path2);
                    
                    $debugInfo['image_files_exist'][$img] = [
                        'in_storage_advertisements' => $exists1,
                        'in_images_advertisements' => $exists2,
                        'path1' => $path1,
                        'path2' => $path2
                    ];
                }
            }
            
            // Write debug info to a file
            file_put_contents(
                base_path('tests/Browser/screenshots/image-debug-info.json'),
                json_encode($debugInfo, JSON_PRETTY_PRINT)
            );
        });
    }
}
