<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomePageTest extends DuskTestCase
{
    /**
     * Test that the homepage loads.
     *
     * @return void
     */
    public function testBasicPageLoad()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSeeIn('html', '');
        });
    }

    /**
     * Test that we can navigate to the login page.
     *
     * @return void
     */
    public function testNavigateToLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->assertSeeIn('html', '');
        });
    }
}
