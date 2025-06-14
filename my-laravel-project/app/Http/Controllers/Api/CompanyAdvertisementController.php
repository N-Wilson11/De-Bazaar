<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyAdvertisementController extends Controller
{
    /**
     * Haal alle advertenties op voor het geauthenticeerde bedrijf
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Alleen de advertenties van het ingelogde bedrijf ophalen
        $user = Auth::user();
        if ($user->user_type !== 'zakelijk') {
            return response()->json([
                'success' => false,
                'message' => 'Alleen zakelijke accounts hebben toegang tot deze API',
            ], 403);
        }

        // Query parameters ophalen
        $status = $request->input('status', 'active'); // Default status is 'active'
        $type = $request->input('type'); // Optional filter by type (normaal/huur)
        $isRental = $request->boolean('is_rental'); // Optional filter by rental status
        $category = $request->input('category'); // Optional filter by category
        $sortBy = $request->input('sort_by', 'created_at'); // Default sorting by created_at
        $sortDirection = $request->input('sort_direction', 'desc'); // Default sorting direction
        $perPage = $request->input('per_page', 15); // Default pagination: 15 items per page

        // Query bouwen
        $query = Advertisement::where('user_id', $user->id)
            ->where('status', $status);

        // Optionele filters toepassen
        if ($type) {
            $query->where('type', $type);
        }

        if ($request->has('is_rental')) {
            $query->where('is_rental', $isRental);
        }

        if ($category) {
            $query->where('category', $category);
        }

        // Sorteer opties toepassen
        $allowedSortFields = ['created_at', 'price', 'title', 'expires_at'];
        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'created_at';
        $sortDirection = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Paginatie toepassen
        $advertisements = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $advertisements,
            'meta' => [
                'total' => $advertisements->total(),
                'per_page' => $advertisements->perPage(),
                'current_page' => $advertisements->currentPage(),
                'last_page' => $advertisements->lastPage(),
                'from' => $advertisements->firstItem(),
                'to' => $advertisements->lastItem(),
            ],
        ]);
    }

    /**
     * Haal een specifieke advertentie op voor het geauthenticeerde bedrijf
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user();
        if ($user->user_type !== 'zakelijk') {
            return response()->json([
                'success' => false,
                'message' => 'Alleen zakelijke accounts hebben toegang tot deze API',
            ], 403);
        }

        $advertisement = Advertisement::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$advertisement) {
            return response()->json([
                'success' => false,
                'message' => 'Advertentie niet gevonden of je hebt geen toegang tot deze advertentie',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $advertisement,
        ]);
    }

    /**
     * Maak een nieuwe advertentie voor het geauthenticeerde bedrijf
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type !== 'zakelijk') {
            return response()->json([
                'success' => false,
                'message' => 'Alleen zakelijke accounts hebben toegang tot deze API',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|string|in:nieuw,als nieuw,goed,gebruikt,met gebreken',
            'category' => 'required|string|max:100',
            'type' => 'required|string|in:normaal,huur',
            'location' => 'required|string|max:100',
            'is_rental' => 'boolean',
            'rental_price_day' => 'required_if:is_rental,true|nullable|numeric|min:0',
            'rental_price_week' => 'nullable|numeric|min:0',
            'rental_price_month' => 'nullable|numeric|min:0',
            'minimum_rental_days' => 'nullable|integer|min:1',
            'rental_conditions' => 'nullable|string',
            'rental_requires_deposit' => 'nullable|boolean',
            'rental_deposit_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validatie fout',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Type en is_rental velden consistent maken
        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['status'] = 'active';
        
        // Set is_rental based on type if not explicitly provided
        if (!isset($data['is_rental'])) {
            $data['is_rental'] = $data['type'] === 'huur';
        }

        // Default rental fields for non-rental advertisements
        if (!$data['is_rental']) {
            $data['rental_price_day'] = null;
            $data['rental_price_week'] = null;
            $data['rental_price_month'] = null;
            $data['minimum_rental_days'] = null;
            $data['rental_conditions'] = null;
            $data['rental_requires_deposit'] = false;
            $data['rental_deposit_amount'] = null;
        }

        // Create advertisement
        $advertisement = Advertisement::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Advertentie succesvol aangemaakt',
            'data' => $advertisement,
        ], 201);
    }

    /**
     * Update een bestaande advertentie van het geauthenticeerde bedrijf
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->user_type !== 'zakelijk') {
            return response()->json([
                'success' => false,
                'message' => 'Alleen zakelijke accounts hebben toegang tot deze API',
            ], 403);
        }

        $advertisement = Advertisement::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$advertisement) {
            return response()->json([
                'success' => false,
                'message' => 'Advertentie niet gevonden of je hebt geen toegang tot deze advertentie',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'condition' => 'nullable|string|in:nieuw,als nieuw,goed,gebruikt,met gebreken',
            'category' => 'nullable|string|max:100',
            'type' => 'nullable|string|in:normaal,huur',
            'status' => 'nullable|string|in:active,inactive,sold,expired',
            'location' => 'nullable|string|max:100',
            'is_rental' => 'nullable|boolean',
            'rental_price_day' => 'nullable|numeric|min:0',
            'rental_price_week' => 'nullable|numeric|min:0',
            'rental_price_month' => 'nullable|numeric|min:0',
            'minimum_rental_days' => 'nullable|integer|min:1',
            'rental_conditions' => 'nullable|string',
            'rental_requires_deposit' => 'nullable|boolean',
            'rental_deposit_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validatie fout',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update advertisement
        $advertisement->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Advertentie succesvol bijgewerkt',
            'data' => $advertisement,
        ]);
    }

    /**
     * Verwijder een advertentie van het geauthenticeerde bedrijf
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->user_type !== 'zakelijk') {
            return response()->json([
                'success' => false,
                'message' => 'Alleen zakelijke accounts hebben toegang tot deze API',
            ], 403);
        }

        $advertisement = Advertisement::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$advertisement) {
            return response()->json([
                'success' => false,
                'message' => 'Advertentie niet gevonden of je hebt geen toegang tot deze advertentie',
            ], 404);
        }

        $advertisement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Advertentie succesvol verwijderd',
        ]);
    }
}
