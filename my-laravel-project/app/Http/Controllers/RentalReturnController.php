<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RentalReturnController extends Controller
{
    /**
     * Toon het formulier voor het terugbrengen van een gehuurd product
     *
     * @param OrderItem $orderItem
     * @return \Illuminate\View\View
     */    public function showReturnForm(OrderItem $orderItem)
    {
        // Controleer of de gebruiker de eigenaar is van de bestelling
        $this->authorize('return-orderItem', $orderItem);

        // Controleer of het item een huurproduct is dat nog niet is teruggebracht
        if (!$orderItem->is_rental || $orderItem->is_returned) {
            return redirect()->back()->with('error', 'Dit product kan niet worden teruggebracht.');
        }

        return view('rentals.return_form', compact('orderItem'));
    }

    /**
     * Verwerk het terugbrengen van een gehuurd product
     *
     * @param Request $request
     * @param OrderItem $orderItem
     * @return \Illuminate\Http\RedirectResponse
     */    public function processReturn(Request $request, OrderItem $orderItem)
    {        // Controleer of de gebruiker de eigenaar is van de bestelling
        $this->authorize('return-orderItem', $orderItem);

        // Controleer of het item een huurproduct is dat nog niet is teruggebracht
        if (!$orderItem->is_rental || $orderItem->is_returned) {
            return redirect()->back()->with('error', 'Dit product kan niet worden teruggebracht.');
        }

        // Valideer het verzoek
        $request->validate([
            'return_photo' => 'required|image|max:5120', // Max 5MB
            'return_notes' => 'nullable|string|max:1000',
            'return_condition' => 'required|in:excellent,good,fair,poor', // Conditie van het product
        ]);

        // Upload de foto
        $path = $request->file('return_photo')->store('return_photos', 'public');
          // Bereken slijtage als deze functionaliteit is ingeschakeld voor de advertentie
        $wearAndTearAmount = 0;
        $advertisement = $orderItem->advertisement;
        $depositRefundedAmount = $orderItem->deposit_amount ?? 0;
        
        if ($advertisement && $advertisement->rental_calculate_wear_and_tear) {
            $startDate = Carbon::parse($orderItem->rental_start_date);
            $endDate = Carbon::parse($orderItem->rental_end_date);
            $condition = $request->return_condition;
            
            // Bereken slijtage op basis van de duur en conditie
            $wearAndTearAmount = $advertisement->calculateWearAndTear($startDate, $endDate, $condition);
            
            // Bereken terug te betalen borg (borg - slijtage)
            if ($orderItem->deposit_amount > 0) {
                $depositRefundedAmount = max(0, $orderItem->deposit_amount - $wearAndTearAmount);
            }
            
            Log::info('Calculated wear and tear amount', [
                'order_item_id' => $orderItem->id,
                'advertisement_id' => $advertisement->id,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'condition' => $condition,
                'amount' => $wearAndTearAmount,
                'deposit' => $orderItem->deposit_amount,
                'deposit_refunded' => $depositRefundedAmount
            ]);
        }        // Update het orderItem
        $orderItem->update([
            'is_returned' => true,
            'returned_at' => Carbon::now(),
            'return_photo' => $path,
            'return_notes' => $request->return_notes,
            'return_condition' => $request->return_condition,
            'wear_and_tear_amount' => $wearAndTearAmount,
            'deposit_refunded_amount' => $depositRefundedAmount,
        ]);        // Bereid succes bericht voor, inclusief slijtage informatie indien van toepassing
        $successMessage = 'Het product is succesvol teruggebracht. Bedankt!';
        
        if ($orderItem->deposit_amount > 0) {
            if ($wearAndTearAmount > 0) {
                $successMessage .= ' Er is €' . number_format($wearAndTearAmount, 2, ',', '.') . 
                    ' aan slijtage berekend, waardoor er €' . number_format($depositRefundedAmount, 2, ',', '.') . 
                    ' van de oorspronkelijke borg (€' . number_format($orderItem->deposit_amount, 2, ',', '.') . ') wordt terugbetaald.';
            } else {
                $successMessage .= ' De volledige borg van €' . number_format($orderItem->deposit_amount, 2, ',', '.') . 
                    ' wordt terugbetaald.';
            }
        } elseif ($wearAndTearAmount > 0) {
            $successMessage .= ' Er is €' . number_format($wearAndTearAmount, 2, ',', '.') . 
                ' aan slijtage berekend op basis van de huurperiode en de opgegeven conditie.';
        }
        
        // Notificatie naar verkoper zou hier kunnen worden toegevoegd
        
        return redirect()->route('orders.show', $orderItem->order_id)
            ->with('success', $successMessage);
    }
    
    /**
     * Toon de details van een teruggebracht product (voor de verkoper)
     *
     * @param OrderItem $orderItem
     * @return \Illuminate\View\View
     */    public function showReturnDetails(OrderItem $orderItem)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'U moet ingelogd zijn om deze pagina te bekijken.');
        }
          // Debug info
        Log::info('User trying to view return details', [
            'user_id' => Auth::id(),
            'orderItem_id' => $orderItem->id,
            'seller_id' => $orderItem->seller_id,
            'order_user_id' => $orderItem->order ? $orderItem->order->user_id : 'no_order'
        ]);
        
        // Controleer of de gebruiker de verkoper of de koper is
        if (Auth::id() != $orderItem->seller_id && 
            ($orderItem->order === null || Auth::id() != $orderItem->order->user_id)) {
            return abort(403, 'U heeft geen toegang tot deze pagina. Alleen de verkoper of koper kan deze details bekijken.');
        }

        // Controleer of het item is teruggebracht
        if (!$orderItem->is_rental || !$orderItem->is_returned) {
            return redirect()->back()->with('error', 'Dit product is nog niet teruggebracht.');
        }

        return view('rentals.return_details', compact('orderItem'));
    }
}
