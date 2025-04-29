<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractsController extends Controller
{
    /**
     * Display a listing of the contracts.
     */
    public function index()
    {
        // Als de gebruiker een admin is, toon alle contracten
        $contracts = Contract::with('user')->orderBy('created_at', 'desc')->paginate(10);
        
        return view('contracts.index', compact('contracts'));
    }
    
    /**
     * Show the form for uploading a new contract.
     */
    public function create()
    {
        // Haal alle zakelijke gebruikers op voor de dropdown
        $businessUsers = User::where('user_type', 'zakelijk')->get();
        
        return view('contracts.create', compact('businessUsers'));
    }
    
    /**
     * Store a newly uploaded contract in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'contract_file' => 'required|file|mimes:pdf|max:5120', // Max 5MB PDF files
            'comments' => 'nullable|string|max:500',
        ]);
        
        // Upload het bestand
        $path = $request->file('contract_file')->store('contracts', 'private');
        
        // Genereer een uniek contractnummer
        $user = User::find($request->user_id);
        $contractNumber = 'BZ-' . date('Y') . '-' . str_pad($user->id, 5, '0', STR_PAD_LEFT) . '-' . Str::random(5);
        
        // Maak het contract aan
        Contract::create([
            'user_id' => $request->user_id,
            'contract_number' => $contractNumber,
            'file_path' => $path,
            'comments' => $request->comments,
            'status' => 'pending',
        ]);
        
        return redirect()->route('contracts.index')
            ->with('success', 'Contract succesvol geüpload voor beoordeling.');
    }
    
    /**
     * Display the specified contract.
     */
    public function show(Contract $contract)
    {
        return view('contracts.show', compact('contract'));
    }
    
    /**
     * Download the contract file.
     */
    public function download(Contract $contract)
    {
        // Check if file exists
        if (!Storage::disk('private')->exists($contract->file_path)) {
            return back()->with('error', 'Contract bestand is niet gevonden.');
        }
        
        // Get the file name from the path
        $fileName = basename($contract->file_path);
        
        // Return the file as a download
        return response()->download(
            storage_path('app/private/' . $contract->file_path),
            $fileName
        );
    }
    
    /**
     * Review the contract (approve or reject).
     */
    public function review(Request $request, Contract $contract)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'comments' => 'nullable|string|max:500',
        ]);
        
        $contract->status = $request->status;
        $contract->comments = $request->comments;
        
        if ($request->status === 'approved') {
            $contract->approved_at = now();
            $contract->approved_by = Auth::id();
        }
        
        $contract->save();
        
        $statusText = $request->status === 'approved' ? 'goedgekeurd' : 'afgekeurd';
        
        return redirect()->route('contracts.show', $contract)
            ->with('success', "Contract is {$statusText}.");
    }
    
    /**
     * Remove the specified contract from storage.
     */
    public function destroy(Contract $contract)
    {
        // Verwijder het bestand
        Storage::disk('private')->delete($contract->file_path);
        
        // Verwijder het contract record
        $contract->delete();
        
        return redirect()->route('contracts.index')
            ->with('success', 'Contract is succesvol verwijderd.');
    }
}