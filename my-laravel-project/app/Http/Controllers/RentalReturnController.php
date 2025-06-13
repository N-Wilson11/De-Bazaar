<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
     */
    public function processReturn(Request $request, OrderItem $orderItem)
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
        ]);

        // Upload de foto
        $path = $request->file('return_photo')->store('return_photos', 'public');

        // Update het orderItem
        $orderItem->update([
            'is_returned' => true,
            'returned_at' => Carbon::now(),
            'return_photo' => $path,
            'return_notes' => $request->return_notes,
        ]);

        // Notificatie naar verkoper zou hier kunnen worden toegevoegd

        return redirect()->route('orders.show', $orderItem->order_id)
            ->with('success', 'Het product is succesvol teruggebracht. Bedankt!');
    }
    
    /**
     * Toon de details van een teruggebracht product (voor de verkoper)
     *
     * @param OrderItem $orderItem
     * @return \Illuminate\View\View
     */
    public function showReturnDetails(OrderItem $orderItem)
    {        // Controleer of de gebruiker de verkoper is
        if (Auth::id() != $orderItem->seller_id) {
            abort(403, 'Onbevoegde toegang.');
        }

        // Controleer of het item is teruggebracht
        if (!$orderItem->is_rental || !$orderItem->is_returned) {
            return redirect()->back()->with('error', 'Dit product is nog niet teruggebracht.');
        }

        return view('rentals.return_details', compact('orderItem'));
    }
}
