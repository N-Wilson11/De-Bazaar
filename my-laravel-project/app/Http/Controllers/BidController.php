<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Bid;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BidController extends Controller
{
    /**
     * Toon een lijst van alle biedingen van de huidige gebruiker
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
          $bids = Bid::where('user_id', $user->id)->with('advertisement')->latest()->paginate(10);
        
        return view('bids.index', [
            'bids' => $bids,
            'activeBidsCount' => Bid::where('user_id', $user->id)
                ->where('status', Bid::STATUS_PENDING)
                ->count(),
        ]);
    }
    
    /**
     * Toon formulier om een bod te plaatsen op een advertentie
     *
     * @param Advertisement $advertisement
     * @return \Illuminate\View\View
     */
    public function create(Advertisement $advertisement)
    {
        $user = Auth::user();
        
        // Controleer of de gebruiker al een bod heeft geplaatst
        if ($advertisement->hasBidFrom($user)) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', 'Je hebt al een bod uitgebracht op deze advertentie.');
        }
        
        // Controleer of de advertentie biedingen accepteert
        if (!$advertisement->isAcceptingBids()) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', 'Deze advertentie accepteert momenteel geen biedingen.');
        }
          // Controleer of de gebruiker het maximum aantal biedingen heeft bereikt
        $activeBidsCount = Bid::where('user_id', $user->id)
            ->where('status', Bid::STATUS_PENDING)
            ->count();
            
        if ($activeBidsCount >= 4) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', 'Je hebt het maximale aantal van 4 actieve biedingen bereikt.');
        }
          // Haal het huidige hoogste bod op
        $highestBid = $advertisement->bids()
            ->where('status', Bid::STATUS_PENDING)
            ->orderBy('amount', 'desc')
            ->first();
            
        $minBidAmount = $highestBid 
            ? $highestBid->amount + 0.01 
            : ($advertisement->min_bid_amount ?? $advertisement->price);

        $activeBidsCount = Bid::where('user_id', $user->id)
            ->where('status', Bid::STATUS_PENDING)
            ->count();

        return view('bids.create', [
            'advertisement' => $advertisement,
            'minBidAmount' => $minBidAmount,
            'highestBid' => $highestBid,
            'activeBidsCount' => $activeBidsCount,
        ]);
    }
    
    /**
     * Verwerk een nieuw bod
     *
     * @param Request $request
     * @param Advertisement $advertisement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Advertisement $advertisement)
    {
        $user = Auth::user();
        
        // Valideer invoer
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'message' => 'nullable|string|max:500',
        ]);
          // Controleer of het bod voldoet aan minimumvoorwaarden
        $highestBid = $advertisement->bids()
            ->where('status', Bid::STATUS_PENDING)
            ->orderBy('amount', 'desc')
            ->first();
            
        $minBidAmount = $highestBid 
            ? $highestBid->amount + 0.01 
            : ($advertisement->min_bid_amount ?? $advertisement->price);
            
        if ($validated['amount'] < $minBidAmount) {
            throw ValidationException::withMessages([
                'amount' => ['Je bod moet minimaal ' . number_format($minBidAmount, 2) . ' euro zijn.']
            ]);
        }
        
        // Controleer of de gebruiker al een bod heeft geplaatst
        if ($advertisement->hasBidFrom($user)) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', 'Je hebt al een bod uitgebracht op deze advertentie.');
        }
        
        // Controleer of de advertentie biedingen accepteert
        if (!$advertisement->isAcceptingBids()) {
            return redirect()->route('advertisements.show', $advertisement)
                ->with('error', 'Deze advertentie accepteert momenteel geen biedingen.');
        }
          // Controleer of de gebruiker het maximum aantal biedingen heeft bereikt
        $activeBidsCount = Bid::where('user_id', $user->id)
            ->where('status', Bid::STATUS_PENDING)
            ->count();
            
        if ($activeBidsCount >= 4) {
            return redirect()->route('bids.index')
                ->with('error', 'Je hebt het maximale aantal van 4 actieve biedingen bereikt.');
        }
        
        // Stel een vervaldatum in voor het bod (standaard 7 dagen)
        $expiresAt = Carbon::now()->addDays(7);
        
        // Maak het bod aan
        $bid = new Bid([
            'user_id' => $user->id,
            'advertisement_id' => $advertisement->id,
            'amount' => $validated['amount'],
            'message' => $validated['message'] ?? null,
            'status' => Bid::STATUS_PENDING,
            'expires_at' => $expiresAt,
        ]);
        
        $bid->save();
        
        return redirect()->route('bids.index')
            ->with('success', 'Je bod is succesvol geplaatst.');
    }
    
    /**
     * Toon biedingen voor een bepaalde advertentie (voor de advertentie-eigenaar)
     *
     * @param Advertisement $advertisement
     * @return \Illuminate\View\View
     */
    public function showForAdvertisement(Advertisement $advertisement)
    {
        // Controleer of de gebruiker de eigenaar is
        $this->authorize('manage', $advertisement);
        
        $bids = $advertisement->bids()
            ->with('user')
            ->latest()
            ->paginate(10);
            
        return view('bids.show-for-advertisement', [
            'advertisement' => $advertisement,
            'bids' => $bids,
        ]);
    }
    
    /**
     * Accepteer een bod
     *
     * @param Bid $bid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(Bid $bid)
    {
        // Controleer of de gebruiker de eigenaar van de advertentie is
        $this->authorize('manage', $bid->advertisement);
        
        // Check of het bod nog geldig is
        if (!$bid->isPending()) {
            return redirect()->route('bids.for-advertisement', $bid->advertisement)
                ->with('error', 'Dit bod kan niet meer geaccepteerd worden omdat het niet meer actief is.');
        }
        
        DB::transaction(function () use ($bid) {
            // Update de status van het bod
            $bid->status = Bid::STATUS_ACCEPTED;
            $bid->save();
            
            // Update de status van de advertentie
            $advertisement = $bid->advertisement;
            $advertisement->purchase_status = Advertisement::PURCHASE_STATUS_RESERVED;
            $advertisement->save();
            
            // Wijs alle andere biedingen af
            $advertisement->bids()
                ->where('id', '!=', $bid->id)
                ->where('status', Bid::STATUS_PENDING)
                ->update(['status' => Bid::STATUS_REJECTED]);
        });
        
        return redirect()->route('bids.for-advertisement', $bid->advertisement)
            ->with('success', 'Je hebt het bod geaccepteerd. De advertentie is nu gereserveerd voor de bieder.');
    }
    
    /**
     * Wijs een bod af
     *
     * @param Bid $bid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Bid $bid)
    {
        // Controleer of de gebruiker de eigenaar van de advertentie is
        $this->authorize('manage', $bid->advertisement);
        
        // Check of het bod nog geldig is
        if (!$bid->isPending()) {
            return redirect()->route('bids.for-advertisement', $bid->advertisement)
                ->with('error', 'Dit bod kan niet meer afgewezen worden omdat het niet meer actief is.');
        }
        
        // Update de status van het bod
        $bid->status = Bid::STATUS_REJECTED;
        $bid->save();
        
        return redirect()->route('bids.for-advertisement', $bid->advertisement)
            ->with('success', 'Je hebt het bod afgewezen.');
    }
    
    /**
     * Annuleer een bod (als bieder)
     *
     * @param Bid $bid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Bid $bid)
    {
        // Controleer of de gebruiker de eigenaar van het bod is
        if (Auth::id() !== $bid->user_id) {
            abort(403, 'Je bent niet bevoegd om dit bod te annuleren.');
        }
        
        // Check of het bod nog geldig is
        if (!$bid->isPending()) {
            return redirect()->route('bids.index')
                ->with('error', 'Dit bod kan niet meer geannuleerd worden omdat het niet meer actief is.');
        }
        
        // Update de status van het bod
        $bid->status = Bid::STATUS_REJECTED;
        $bid->save();
        
        return redirect()->route('bids.index')
            ->with('success', 'Je hebt je bod geannuleerd.');
    }
}
