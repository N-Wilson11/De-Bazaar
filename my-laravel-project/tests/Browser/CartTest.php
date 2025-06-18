<?php

namespace Tests\Browser;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CartTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test user login functionality.
     *
     * @return void
     */
    public function testUserLogin()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password')
                    ->press('Log in')
                    ->assertPathIs('/dashboard');
        });
    }

    /**
     * Test adding an item to the cart.
     *
     * @return void
     */
    public function testAddItemToCart()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $advertisement = Advertisement::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Product',
            'price' => 150.00,
            'purchase_status' => 'available',
        ]);

        $this->browse(function (Browser $browser) use ($user, $advertisement) {
            $browser->visit('/login')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password')
                    ->press('Log in')
                    ->visit('/advertisements/' . $advertisement->id)
                    ->press('In winkelwagen')
                    ->assertPathIs('/cart')
                    ->assertSee('Test Product')
                    ->assertSee('€150.00');
        });
    }

    /**
     * Test viewing the cart page.
     *
     * @return void
     */
    public function testViewCart()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password')
                    ->press('Log in')
                    ->visit('/cart')
                    ->assertSee('Winkelwagen');
        });
    }

    /**
     * Test checking out from the cart.
     *
     * @return void
     */
    public function testCheckout()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $advertisement = Advertisement::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Product',
            'price' => 150.00,
            'purchase_status' => 'available',
        ]);

        $this->browse(function (Browser $browser) use ($user, $advertisement) {
            $browser->visit('/login')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password')
                    ->press('Log in')
                    ->visit('/advertisements/' . $advertisement->id)
                    ->press('In winkelwagen')
                    ->assertPathIs('/cart')
                    ->assertSee('Test Product')
                    ->press('Afrekenen')
                    ->assertPathIs('/checkout')
                    ->assertSee('Betaling');
        });
    }
}
