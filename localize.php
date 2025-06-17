<?php

// Script to localize all views and fix route parameters issues

// Define the base directory
$baseDir = __DIR__ . '/my-laravel-project/resources/views';

// Common replacements to make
$replacements = [
    // Home page
    'Welkom bij De-Bazaar' => "{{ __('general.welcome') }}",
    'Ontdek de beste producten van onze verkopers en verhuurders.' => "{{ __('general.discover_best_products') }}",
    'Registreer nu om jouw producten aan te bieden of om te zoeken naar wat je nodig hebt.' => "{{ __('general.register_now_info') }}",
    'Registreren' => "{{ __('general.register') }}",
    'Inloggen' => "{{ __('general.login') }}",
    'Nieuwste Producten' => "{{ __('general.newest_products') }}",
    'Bekijk Details' => "{{ __('general.view_details') }}",
    'Advertentie niet beschikbaar' => "{{ __('general.advertisement_unavailable') }}",
    'Bekijk Alle Producten' => "{{ __('general.view_all_products') }}",
    'Verhuur Aanbiedingen' => "{{ __('general.rental_offers') }}",
    'Verhuur' => "{{ __('general.rental') }}",
    'Bekijk Alle Verhuur' => "{{ __('general.view_all_rentals') }}",
    'dag' => "__('general.day')",
    
    // Fix route parameters
    "route('advertisements.show', \$advertisement)" => "route('advertisements.show', \$advertisement->id)",
    "route('advertisements.show', \$rental)" => "route('advertisements.show', \$rental->id)",
];

// Function to process a file
function processFile($file, $replacements) {
    echo "Processing file: $file\n";
    $content = file_get_contents($file);
    
    $originalContent = $content;
    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "Updated file: $file\n";
    }
}

// Function to recursively process directories
function processDirectory($dir, $replacements) {
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            processDirectory($path, $replacements);
        } else if (pathinfo($path, PATHINFO_EXTENSION) === 'php' || 
                   pathinfo($path, PATHINFO_EXTENSION) === 'blade.php') {
            processFile($path, $replacements);
        }
    }
}

// Add translations to the language files
function addTranslations() {
    // English translations
    $enTranslationsFile = __DIR__ . '/my-laravel-project/resources/lang/en/general.php';
    $enTranslations = include($enTranslationsFile);
    
    $additionalEnTranslations = [
        'discover_best_products' => 'Discover the best products from our sellers and renters.',
        'register_now_info' => 'Register now to offer your products or to find what you need.',
        'newest_products' => 'Newest Products',
        'view_details' => 'View Details',
        'advertisement_unavailable' => 'Advertisement not available',
        'view_all_products' => 'View All Products',
        'rental_offers' => 'Rental Offers',
        'rental' => 'Rental',
        'view_all_rentals' => 'View All Rentals',
    ];
    
    foreach ($additionalEnTranslations as $key => $value) {
        if (!isset($enTranslations[$key])) {
            $enTranslations[$key] = $value;
        }
    }
    
    // Write back to file
    $enContent = "<?php\n\nreturn " . var_export($enTranslations, true) . ";\n";
    file_put_contents($enTranslationsFile, $enContent);
    
    // Dutch translations
    $nlTranslationsFile = __DIR__ . '/my-laravel-project/resources/lang/nl/general.php';
    $nlTranslations = include($nlTranslationsFile);
    
    $additionalNlTranslations = [
        'discover_best_products' => 'Ontdek de beste producten van onze verkopers en verhuurders.',
        'register_now_info' => 'Registreer nu om jouw producten aan te bieden of om te zoeken naar wat je nodig hebt.',
        'newest_products' => 'Nieuwste Producten',
        'view_details' => 'Bekijk Details',
        'advertisement_unavailable' => 'Advertentie niet beschikbaar',
        'view_all_products' => 'Bekijk Alle Producten',
        'rental_offers' => 'Verhuur Aanbiedingen',
        'rental' => 'Verhuur',
        'view_all_rentals' => 'Bekijk Alle Verhuur',
    ];
    
    foreach ($additionalNlTranslations as $key => $value) {
        if (!isset($nlTranslations[$key])) {
            $nlTranslations[$key] = $value;
        }
    }
    
    // Write back to file
    $nlContent = "<?php\n\nreturn " . var_export($nlTranslations, true) . ";\n";
    file_put_contents($nlTranslationsFile, $nlContent);
    
    echo "Added translations to language files.\n";
}

// Run the script
echo "Starting localization...\n";
processDirectory($baseDir, $replacements);
addTranslations();
echo "Localization complete!\n";
