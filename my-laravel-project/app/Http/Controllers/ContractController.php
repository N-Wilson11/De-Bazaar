<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ContractController extends Controller
{
    public function __construct()
    {
        // Middleware will be applied at the route level
    }

    /**
     * Generate and download a business registration contract as PDF.
     * 
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    public function generateBusinessContract($userId)
    {
        // Get the user
        $user = User::findOrFail($userId);
        
        // Check if user is a business user
        if ($user->user_type !== 'zakelijk') {
            return back()->with('error', 'Alleen voor zakelijke accounts kan een contract worden gegenereerd.');
        }
        
        // Generate contract number
        $contractNumber = 'BZ-' . date('Y') . '-' . str_pad($user->id, 5, '0', STR_PAD_LEFT);
        
        // Current date
        $date = Carbon::now()->format('d-m-Y');
        
        // Generate PDF
        $pdf = PDF::loadView('contracts.business_registration', [
            'user' => $user,
            'contractNumber' => $contractNumber,
            'date' => $date
        ]);
        
        // Return for download
        return $pdf->download('contract_' . $user->id . '_' . date('Ymd') . '.pdf');
    }
}