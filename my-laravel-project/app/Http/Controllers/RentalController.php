<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class RentalController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }    /**
     * Show the form to rent an advertisement for specific dates.
     *
     * @param Advertisement $advertisement
     * @return \Illuminate\View\View
     */
    public function showRentForm(Advertisement $advertisement)
    {
        // Controleer of dit een huuradvertentie is
        if (!$advertisement->isRental()) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', __('Dit product is niet beschikbaar voor verhuur.'));
        }
        
        // Controleer of de advertentie actief is
        if ($advertisement->status !== 'active') {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', __('Deze advertentie is niet meer actief.'));
        }
        
        // Get available dates
        $availability = is_array($advertisement->rental_availability) ? $advertisement->rental_availability : [];
        $bookedDates = is_array($advertisement->rental_booked_dates) ? $advertisement->rental_booked_dates : [];
        
        try {
            return view('rentals.rent_form', compact('advertisement', 'availability', 'bookedDates'));
        } catch (\Exception $e) {
            Log::error('Fout bij laden huurformulier: ' . $e->getMessage());
            
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', __('Er is een fout opgetreden bij het laden van het huurformulier. Probeer het later opnieuw.'));
        }
    }
    
    /**
     * Process a rental request.
     *
     * @param Advertisement $advertisement
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processRental(Advertisement $advertisement, Request $request)
    {
        // Valideer de invoergegevens
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Controleer of dit een huuradvertentie is
        if (!$advertisement->isRental()) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', __('Dit product is niet beschikbaar voor verhuur.'));
        }
        
        // Controleer of je je eigen advertentie niet probeert te huren
        if (Auth::id() === $advertisement->user_id) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', __('Je kunt je eigen producten niet huren.'));
        }
        
        // Controleer of de geselecteerde dagen beschikbaar zijn
        if (!$advertisement->isAvailableForDates($startDate, $endDate)) {
            return redirect()->route('rentals.rent', $advertisement)
                ->with('error', __('De geselecteerde data zijn niet beschikbaar voor verhuur.'));
        }
        
        // Bereken de huurprijs voor de geselecteerde periode
        $rentalPrice = $advertisement->calculateRentalPrice($startDate, $endDate);
        if ($rentalPrice === null) {
            return redirect()->route('rentals.rent', $advertisement)
                ->with('error', __('De minimale huurtermijn is niet voldaan of er is een probleem met de prijsberekening.'));
        }
        
        // Begin een database transactie
        DB::beginTransaction();
          try {
            // Maak een nieuwe order aan
            $order = new Order();
            $order->user_id = Auth::id();
            $order->total_amount = $rentalPrice;
            $order->status = Order::STATUS_PENDING;
            $order->payment_method = 'nog te bepalen'; // Dit kan later worden bijgewerkt
            $order->shipping_address = null; // Niet relevant voor verhuur
            $order->billing_address = null; // Dit kan later worden bijgewerkt
            $order->notes = 'Verhuur van ' . $advertisement->title . ' van ' . $startDate . ' tot ' . $endDate;
            $order->save();
              // Maak een order item aan voor de verhuur
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->advertisement_id = $advertisement->id;
            $orderItem->seller_id = $advertisement->user_id; // Voeg verkoper ID toe
            $orderItem->title = $advertisement->title; // Voeg titel toe
            $orderItem->quantity = 1;
            $orderItem->price = $rentalPrice;
            // Sla borg op als die vereist is
            if ($advertisement->rental_requires_deposit && $advertisement->rental_deposit_amount > 0) {
                $orderItem->deposit_amount = $advertisement->rental_deposit_amount;
            }
            $orderItem->is_rental = true;
            $orderItem->rental_start_date = $startDate;
            $orderItem->rental_end_date = $endDate;
            $orderItem->save();
            
            // Werk de geboekte datums bij in de advertentie
            $startDateObj = Carbon::parse($startDate);
            $endDateObj = Carbon::parse($endDate);
            $rentalPeriod = [];
            
            for ($date = clone $startDateObj; $date->lte($endDateObj); $date->addDay()) {
                $rentalPeriod[] = $date->format('Y-m-d');
            }
            
            $bookedDates = is_array($advertisement->rental_booked_dates) ? $advertisement->rental_booked_dates : [];
            $advertisement->rental_booked_dates = array_values(array_unique(array_merge($bookedDates, $rentalPeriod)));
            $advertisement->save();
            
            // Commit de transactie
            DB::commit();
            
            return redirect()->route('orders.show', $order)
                ->with('success', __('Je huuraanvraag is succesvol ingediend. Betaal om de reservering te voltooien.'));
            
        } catch (\Exception $e) {
            // Rollback bij problemen
            DB::rollBack();
              // Log de fout voor debugging
            Log::error('Fout bij verhuurproces: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->route('rentals.rent', $advertisement)
                ->with('error', __('Er is een fout opgetreden bij het verwerken van je huuraanvraag: ') . $e->getMessage());
        }
    }
    
    /**
     * Show a calendar overview of the user's rented items
     * 
     * @return \Illuminate\View\View
     */
    public function rentalCalendar()
    {
        // Get the current authenticated user
        $user = Auth::user();
          // Get all order items for this user that are rentals with future dates
        $upcomingRentals = OrderItem::whereIn('order_id', function($query) use ($user) {
                $query->select('id')
                    ->from('orders')
                    ->where('user_id', $user->id);
            })
            ->where('is_rental', true)
            ->with(['order', 'advertisement'])
            ->orderBy('rental_start_date', 'asc')
            ->get();
            
        // Get the current month's rental items
        $today = Carbon::today();
        $startOfMonth = Carbon::today()->startOfMonth();
        $endOfMonth = Carbon::today()->endOfMonth();
        
        $currentMonthRentals = $upcomingRentals->filter(function($rental) use ($startOfMonth, $endOfMonth) {
            $startDate = Carbon::parse($rental->rental_start_date);
            $endDate = Carbon::parse($rental->rental_end_date);
            
            return ($startDate->between($startOfMonth, $endOfMonth) || 
                   $endDate->between($startOfMonth, $endOfMonth) ||
                   ($startDate->lte($startOfMonth) && $endDate->gte($endOfMonth)));
        });
        
        // Get the next month's rental items
        $nextMonthStart = Carbon::today()->addMonth()->startOfMonth();
        $nextMonthEnd = Carbon::today()->addMonth()->endOfMonth();
        
        $nextMonthRentals = $upcomingRentals->filter(function($rental) use ($nextMonthStart, $nextMonthEnd) {
            $startDate = Carbon::parse($rental->rental_start_date);
            $endDate = Carbon::parse($rental->rental_end_date);
            
            return ($startDate->between($nextMonthStart, $nextMonthEnd) || 
                   $endDate->between($nextMonthStart, $nextMonthEnd) ||
                   ($startDate->lte($nextMonthStart) && $endDate->gte($nextMonthEnd)));
        });
        
        return view('rentals.calendar', compact('upcomingRentals', 'currentMonthRentals', 'nextMonthRentals', 'today'));
    }
    
    /**
     * Show a calendar overview of the advertiser's rented out items
     * 
     * @return \Illuminate\View\View
     */
    public function advertiserRentalCalendar()
    {
        // Get the current authenticated user (advertiser)
         $user = Auth::user();
        
        // Get all order items where the current user is the seller and items are rentals
        $rentedOutItems = OrderItem::where('seller_id', $user->id)
            ->where('is_rental', true)
            ->with(['order.user', 'advertisement'])
            ->orderBy('rental_start_date', 'asc')
            ->get();
        
        // Filter only to orders that are not cancelled
        $rentedOutItems = $rentedOutItems->filter(function($item) {
            return $item->order->status !== Order::STATUS_CANCELLED;
        });
            
        // Get today's date and current month range
        $today = Carbon::today();
        $startOfMonth = Carbon::today()->startOfMonth();
        $endOfMonth = Carbon::today()->endOfMonth();
        
        // Filter for current month rentals
        $currentMonthRentals = $rentedOutItems->filter(function($rental) use ($startOfMonth, $endOfMonth) {
            $startDate = Carbon::parse($rental->rental_start_date);
            $endDate = Carbon::parse($rental->rental_end_date);
            
            return ($startDate->between($startOfMonth, $endOfMonth) || 
                   $endDate->between($startOfMonth, $endOfMonth) ||
                   ($startDate->lte($startOfMonth) && $endDate->gte($endOfMonth)));
        });
        
        // Filter for next month rentals
        $nextMonthStart = Carbon::today()->addMonth()->startOfMonth();
        $nextMonthEnd = Carbon::today()->addMonth()->endOfMonth();
        
        $nextMonthRentals = $rentedOutItems->filter(function($rental) use ($nextMonthStart, $nextMonthEnd) {
            $startDate = Carbon::parse($rental->rental_start_date);
            $endDate = Carbon::parse($rental->rental_end_date);
            
            return ($startDate->between($nextMonthStart, $nextMonthEnd) || 
                   $endDate->between($nextMonthStart, $nextMonthEnd) ||
                   ($startDate->lte($nextMonthStart) && $endDate->gte($nextMonthEnd)));
        });
        
        // Group rentals by advertisement for easier organization
        $rentalsByProduct = $rentedOutItems->groupBy('advertisement_id');
        
        return view('rentals.advertiser_calendar', compact(
            'rentedOutItems', 
            'currentMonthRentals', 
            'nextMonthRentals', 
            'rentalsByProduct',
            'today'
        ));
    }
}
