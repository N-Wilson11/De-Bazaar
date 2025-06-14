<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyTheme;
use Illuminate\Support\Str;

class CompanyThemeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a business user can only see and edit their own company theme.
     *
     * @return void
     */
    public function test_business_user_can_only_access_own_theme()
    {
        // Maak twee bedrijven aan
        $company1 = Company::create([
            'name' => 'Test Bedrijf 1',
            'slug' => 'test-bedrijf-1',
            'email' => 'info@testbedrijf1.nl',
            'description' => 'Test Bedrijf 1 Beschrijving',
            'is_active' => true
        ]);
        
        $company2 = Company::create([
            'name' => 'Test Bedrijf 2',
            'slug' => 'test-bedrijf-2',
            'email' => 'info@testbedrijf2.nl',
            'description' => 'Test Bedrijf 2 Beschrijving',
            'is_active' => true
        ]);
        
        // Maak voor elk bedrijf een thema aan
        $theme1 = CompanyTheme::create([
            'company_id' => $company1->id,
            'name' => $company1->name,
            'primary_color' => '#FF0000', // Rood
            'secondary_color' => '#00FF00', // Groen
            'accent_color' => '#0000FF', // Blauw
            'text_color' => '#000000',
            'background_color' => '#FFFFFF',
            'footer_text' => '© ' . date('Y') . ' ' . $company1->name,
            'is_active' => true
        ]);
        
        $theme2 = CompanyTheme::create([
            'company_id' => $company2->id,
            'name' => $company2->name,
            'primary_color' => '#FFFF00', // Geel
            'secondary_color' => '#00FFFF', // Cyan
            'accent_color' => '#FF00FF', // Magenta
            'text_color' => '#FFFFFF',
            'background_color' => '#000000',
            'footer_text' => '© ' . date('Y') . ' ' . $company2->name,
            'is_active' => true
        ]);
        
        // Maak voor elk bedrijf een zakelijke gebruiker
        $user1 = User::factory()->create([
            'name' => 'Bedrijfsgebruiker 1',
            'email' => 'user1@testbedrijf1.nl',
            'user_type' => 'zakelijk',
            'company_id' => $company1->id
        ]);
        
        $user2 = User::factory()->create([
            'name' => 'Bedrijfsgebruiker 2',
            'email' => 'user2@testbedrijf2.nl',
            'user_type' => 'zakelijk',
            'company_id' => $company2->id
        ]);
        
        // Login als eerste zakelijke gebruiker
        $response = $this->actingAs($user1)->get(route('theme.settings'));
        
        // Controleer of alleen het thema van bedrijf 1 zichtbaar is
        $response->assertSee($theme1->primary_color)
                ->assertDontSee($theme2->primary_color);
        
        // Wijzig het thema als eerste zakelijke gebruiker
        $response = $this->actingAs($user1)->post(route('theme.update'), [
            'name' => $company1->name,
            'primary_color' => '#990000', // Donkerrood
            'secondary_color' => $theme1->secondary_color,
            'accent_color' => $theme1->accent_color,
            'text_color' => $theme1->text_color,
            'background_color' => $theme1->background_color,
            'footer_text' => $theme1->footer_text
        ]);
        
        // Controleer of het thema van bedrijf 1 is bijgewerkt
        $theme1->refresh();
        $this->assertEquals('#990000', $theme1->primary_color);
        
        // Controleer of het thema van bedrijf 2 niet is gewijzigd
        $theme2->refresh();
        $this->assertEquals('#FFFF00', $theme2->primary_color);
        
        // Login als tweede zakelijke gebruiker
        $response = $this->actingAs($user2)->get(route('theme.settings'));
        
        // Controleer of alleen het thema van bedrijf 2 zichtbaar is
        $response->assertSee($theme2->primary_color)
                ->assertDontSee($theme1->primary_color);
    }
}
