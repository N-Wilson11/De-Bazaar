<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AdvertisementImagesSeeder extends Seeder
{    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create advertisement images directory if it doesn't exist
        $targetDir = public_path('images/advertisements');
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }
        
        // Create sample-images directory if it doesn't exist
        $sampleDir = resource_path('sample-images');
        if (!File::isDirectory($sampleDir)) {
            File::makeDirectory($sampleDir, 0755, true);
        }

        // Define the image mappings (filename => product)
        $imageMap = [
            'sofa.jpg' => 'Modern beige sofa',
            'tv.jpg' => 'LG OLED TV',
            'golf.jpg' => 'Callaway golf set',
            'desk.jpg' => 'Adjustable desk',
            'tablesaw.jpg' => 'DeWalt table saw',
            'mower.jpg' => 'Makita robotic mower',
            'drone.jpg' => '4K drone',
            'ebike.jpg' => 'Electric bike',
        ];

        // Copy the sample images to the public directory
        foreach ($imageMap as $filename => $description) {
            // Check if images exist in the source location
            $sourcePath = resource_path('sample-images/' . $filename);
            $targetPath = $targetDir . '/' . $filename;

            // First try from a sample-images directory in resources
            if (File::exists($sourcePath)) {
                File::copy($sourcePath, $targetPath);
                $this->command->info("Copied {$filename} to advertisements folder");
            } else {
                // If no source file, create a placeholder with description
                $this->command->warn("{$filename} not found in sample-images directory. Creating placeholder.");
                
                // Generate a colored placeholder
                $img = $this->createPlaceholder($description);
                
                // Save the image
                imagepng($img, $targetPath);
                imagedestroy($img);
                
                $this->command->info("Created placeholder for {$filename}");
            }
        }

        $this->command->info('Advertisement images have been setup successfully.');
    }

    /**
     * Create a simple placeholder image with text
     */
    private function createPlaceholder($text)
    {
        // Create a 600x400 image
        $width = 600;
        $height = 400;
        $image = imagecreatetruecolor($width, $height);
        
        // Colors
        $bgColor = imagecolorallocate($image, rand(200, 255), rand(200, 255), rand(200, 255));
        $textColor = imagecolorallocate($image, 0, 0, 0);
        
        // Fill background
        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);
        
        // Add text
        $fontSize = 5;
        $text = wordwrap($text, 20, "\n", true);
        
        // Calculate position for centered text
        $textBoundingBox = imagettfbbox($fontSize, 0, 'arial', $text);
        $textWidth = $textBoundingBox[2] - $textBoundingBox[0];
        $textHeight = $textBoundingBox[1] - $textBoundingBox[7];
        $textX = ($width - $textWidth) / 2;
        $textY = ($height - $textHeight) / 2;
        
        // Add the text
        imagestring($image, $fontSize, $textX, $textY, $text, $textColor);
        
        return $image;
    }
}
