<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display the user's cart.
     */
    public function index()
    {
        $user = Auth::user();
        $cart = $user->activeCart;
        
        // If user doesn't have an active cart, create one
        if (!$cart) {
            $cart = new Cart();
            $cart->user_id = $user->id;
            $cart->status = 'active';
            $cart->save();
        }
        
        return view('cart.index', compact('cart'));
    }
      /**
     * Add an item to the cart.
     */
    public function addItem(Request $request, Advertisement $advertisement)
    {
        $user = Auth::user();
        
        // Verhuurproducten kunnen niet gekocht worden
        if ($advertisement->isRental()) {
            return redirect()->back()->with('error', __('Dit is een verhuurproduct en kan niet gekocht worden.'));
        }
        
        // Check if advertisement is available for purchase
        if (!$advertisement->isAvailableForPurchase()) {
            return redirect()->back()->with('error', __('Deze advertentie is niet beschikbaar voor aankoop.'));
        }
        
        // Prevent users from adding their own advertisements to cart
        if ($advertisement->user_id === $user->id) {
            return redirect()->back()->with('error', __('Je kunt je eigen advertenties niet kopen.'));
        }
        
        // Get active cart or create a new one
        $cart = $user->activeCart;
        if (!$cart) {
            $cart = new Cart();
            $cart->user_id = $user->id;
            $cart->status = 'active';
            $cart->save();
        }
        
        // Check if the item already exists in the cart
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('advertisement_id', $advertisement->id)
            ->first();
            
        if ($existingItem) {
            return redirect()->back()->with('info', __('Deze advertentie staat al in je winkelwagen.'));
        } else {
            // Add new item to cart
            $cartItem = new CartItem();
            $cartItem->cart_id = $cart->id;
            $cartItem->advertisement_id = $advertisement->id;
            $cartItem->quantity = 1;
            $cartItem->save();
        }
        
        return redirect()->route('cart.index')
            ->with('success', __('Product toegevoegd aan winkelwagen!'));
    }
    
    /**
     * Update the cart item quantity.
     */
    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        $user = Auth::user();
        
        // Check if the cart belongs to the user
        if ($cartItem->cart->user_id !== $user->id) {
            return redirect()->route('cart.index')
                ->with('error', __('Je hebt geen toegang tot dit item.'));
        }
        
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);
        
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        
        return redirect()->route('cart.index')
            ->with('success', __('Winkelwagen bijgewerkt!'));
    }
    
    /**
     * Remove an item from the cart.
     */
    public function removeItem(CartItem $cartItem)
    {
        $user = Auth::user();
        
        // Check if the cart belongs to the user
        if ($cartItem->cart->user_id !== $user->id) {
            return redirect()->route('cart.index')
                ->with('error', __('Je hebt geen toegang tot dit item.'));
        }
        
        $cartItem->delete();
        
        return redirect()->route('cart.index')
            ->with('success', __('Item verwijderd uit winkelwagen!'));
    }
    
    /**
     * Clear all items from the cart.
     */
    public function clearCart()
    {
        $user = Auth::user();
        $cart = $user->activeCart;
        
        if ($cart) {
            CartItem::where('cart_id', $cart->id)->delete();
        }
        
        return redirect()->route('cart.index')
            ->with('success', __('Winkelwagen is geleegd!'));
    }
    
    /**
     * Proceed to checkout
     */
    public function checkout()
    {
        $user = Auth::user();
        $cart = $user->activeCart;
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', __('Je winkelwagen is leeg.'));
        }
        
        // Check if all items are still available
        $unavailableItems = [];
        foreach ($cart->items as $item) {
            if (!$item->advertisement->isAvailableForPurchase()) {
                $unavailableItems[] = $item->advertisement->title;
            }
        }
        
        if (!empty($unavailableItems)) {
            return redirect()->route('cart.index')
                ->with('error', __('De volgende items zijn niet meer beschikbaar: ') . implode(', ', $unavailableItems));
        }
        
        return view('cart.checkout', compact('cart'));
    }
}
