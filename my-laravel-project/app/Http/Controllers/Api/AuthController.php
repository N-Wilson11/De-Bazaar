<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Genereer een API token voor toegang tot de bedrijfs-API
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'token_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validatie fout',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Authenticatie controleren
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Ongeldige inloggegevens',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Controleren of de gebruiker een zakelijk account heeft
        if ($user->user_type !== 'zakelijk') {
            return response()->json([
                'success' => false,
                'message' => 'Alleen zakelijke accounts hebben toegang tot deze API',
            ], 403);
        }

        // Token naam instellen (of default gebruiken)
        $tokenName = $request->token_name ?? 'bedrijfs-api-token';

        // Bestaande tokens voor deze naam verwijderen
        $user->tokens()->where('name', $tokenName)->delete();

        // Nieuwe token genereren met de benodigde rechten
        $token = $user->createToken($tokenName, ['company-api']);

        return response()->json([
            'success' => true,
            'message' => 'Login succesvol',
            'token' => $token->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'company_id' => $user->company_id,
            ],
        ]);
    }

    /**
     * Verwijder het huidige API token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token succesvol ingetrokken',
        ]);
    }

    /**
     * Haal alle API tokens op voor de huidige gebruiker
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tokens(Request $request)
    {
        $user = $request->user();
        
        // Alleen basis informatie over de tokens teruggeven (niet de volledige tokens)
        $tokens = $user->tokens->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'created_at' => $token->created_at,
                'last_used_at' => $token->last_used_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $tokens,
        ]);
    }

    /**
     * Verwijder een specifiek API token
     *
     * @param Request $request
     * @param string $tokenId
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeToken(Request $request, $tokenId)
    {
        $user = $request->user();
        
        // Zoek en verwijder het token
        $token = $user->tokens()->where('id', $tokenId)->first();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token niet gevonden',
            ], 404);
        }
        
        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token succesvol ingetrokken',
        ]);
    }
}
