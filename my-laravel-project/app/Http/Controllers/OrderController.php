<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a list of user's orders.
     */
    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()->orderBy('created_at', 'desc')->paginate(10);
        
       return view('orders.index', compact('orders'));
    }
    
    /**
     * Show details of a specific order.
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Check if the order belongs to the user
        if ($order->user_id !== $user->id) {
            return redirect()->route('orders.index')
                ->with('error', __('Je hebt geen toegang tot deze bestelling.'));
        }
        
        return view('orders.show', compact('order'));
    }
    
    /**
     * Place a new order from the cart.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $cart = $user->activeCart;
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', __('Je winkelwagen is leeg.'));
        }
        
        // Validate the checkout form
        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'billing_address' => 'required|string|max:500',
            'payment_method' => 'required|in:ideal,creditcard,banktransfer',
        ]);
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Create new order
            $order = new Order();
            $order->user_id = $user->id;
            $order->total_amount = $cart->getTotalPrice();
            $order->status = Order::STATUS_PENDING;
            $order->payment_method = $request->payment_method;
            $order->shipping_address = $request->shipping_address;
            $order->billing_address = $request->billing_address;
            $order->notes = $request->notes;
            $order->save();
            
            // Add order items
            foreach ($cart->items as $cartItem) {
                // Check if advertisement is still available
                $advertisement = $cartItem->advertisement;
                
                if (!$advertisement->isAvailableForPurchase()) {
                    throw new \Exception(__('Advertentie niet meer beschikbaar: ') . $advertisement->title);
                }
                
                // Create order item
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->advertisement_id = $advertisement->id;
                $orderItem->seller_id = $advertisement->user_id;
                $orderItem->quantity = $cartItem->quantity;
                $orderItem->price = $advertisement->price;
                $orderItem->title = $advertisement->title;
                $orderItem->save();
                
                // Update advertisement status
                $advertisement->purchase_status = Advertisement::PURCHASE_STATUS_SOLD;
                $advertisement->save();
            }
            
            // Clear the cart
            $cart->status = 'completed';
            $cart->save();
            
            // Commit transaction
            DB::commit();
            
            // Redirect to order confirmation
            return redirect()->route('orders.confirmation', $order)
                ->with('success', __('Bestelling succesvol geplaatst!'));
                
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
            
            return redirect()->route('cart.checkout')
                ->with('error', __('Er is een fout opgetreden: ') . $e->getMessage());
        }
    }
    
    /**
     * Show the order confirmation page.
     */
    public function confirmation(Order $order)
    {
        $user = Auth::user();
        
        // Check if the order belongs to the user
        if ($order->user_id !== $user->id) {
            return redirect()->route('orders.index')
                ->with('error', __('Je hebt geen toegang tot deze bestelling.'));
        }
        
        return view('orders.confirmation', compact('order'));
    }
    
    /**
     * Cancel an order.
     */
    public function cancel(Order $order)
    {
        $user = Auth::user();
        
        // Check if the order belongs to the user
        if ($order->user_id !== $user->id) {
            return redirect()->route('orders.index')
                ->with('error', __('Je hebt geen toegang tot deze bestelling.'));
        }
        
        // Only cancel if the order is still in pending status
        if ($order->status !== Order::STATUS_PENDING) {
            return redirect()->route('orders.show', $order)
                ->with('error', __('Deze bestelling kan niet meer geannuleerd worden.'));
        }
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Update order status
            $order->status = Order::STATUS_CANCELLED;
            $order->save();
            
            // Make advertisements available again
            foreach ($order->items as $item) {
                if ($item->advertisement) {
                    $item->advertisement->purchase_status = Advertisement::PURCHASE_STATUS_AVAILABLE;
                    $item->advertisement->save();
                }
            }
            
            // Commit transaction
            DB::commit();
            
            return redirect()->route('orders.show', $order)
                ->with('success', __('Bestelling succesvol geannuleerd.'));
                
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
            
            return redirect()->route('orders.show', $order)
                ->with('error', __('Er is een fout opgetreden: ') . $e->getMessage());
        }
    }

    /**
     * Display the sales for the authenticated seller.
     */
    public function mySales()
    {
        $user = Auth::user();
        
        // Get all order items where the current user is the seller
        $orderItems = OrderItem::where('seller_id', $user->id)
            ->with(['order', 'advertisement'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('orders.my-sales', compact('orderItems'));
    }

    /**
     * Display details of a specific sale item.
     */
    public function showSaleItem(OrderItem $orderItem)
    {
        $user = Auth::user();
        
        // Check if the order item belongs to the seller
        if ($orderItem->seller_id !== $user->id) {
            return redirect()->route('orders.my-sales')
                ->with('error', __('general.no_access_to_this_sale'));
        }
        
        return view('orders.sale-item', compact('orderItem'));
    }

    /**
     * Mark an order item as completed by the seller.
     */
    public function completeSaleItem(Request $request, OrderItem $orderItem)
    {
        $user = Auth::user();
        
        // Check if the order item belongs to the seller
        if ($orderItem->seller_id !== $user->id) {
            return redirect()->route('orders.my-sales')
                ->with('error', __('general.no_access_to_this_sale'));
        }
        
        // Get the parent order
        $order = $orderItem->order;
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Update order status to completed if not already
            if ($order->status !== Order::STATUS_COMPLETED) {
                $order->status = Order::STATUS_COMPLETED;
                $order->save();
            }
            
            // Commit transaction
            DB::commit();
            
            return redirect()->route('orders.show-sale-item', $orderItem)
                ->with('success', __('general.order_completed_successfully'));
                
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
            
            return redirect()->route('orders.show-sale-item', $orderItem)
                ->with('error', __('Er is een fout opgetreden: ') . $e->getMessage());
        }
    }
}
