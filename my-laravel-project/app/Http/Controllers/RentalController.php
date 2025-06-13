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
}
