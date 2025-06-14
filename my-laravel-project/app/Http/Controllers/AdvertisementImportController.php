<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
// We gebruiken native PHP CSV functies in plaats van League\CSV

class AdvertisementImportController extends Controller
{
    /**
     * Create a new controller instance.
     */    public function __construct()
    {
        $this->middleware('auth');
        // Removed business middleware to make it accessible to all authenticated users
    }
    
    /**
     * Toon het uploadformulier voor CSV-importeren.
     *
     * @return \Illuminate\View\View
     */    public function showImportForm()
    {
        // Controleer of de view bestaat
        if (view()->exists('advertisements.import')) {
            return view('advertisements.import');
        }
        
        // Fallback voor debug
        return response()->json([
            'status' => 'success',
            'message' => 'De importfunctie is bereikbaar. De view wordt nu gemaakt.'
        ]);
    }
    
    /**
     * Verwerk de CSV-upload en importeer advertenties.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */    public function processImport(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Import request received', ['has_file' => $request->hasFile('csv_file')]);
        
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);
        
        // CSV-bestand opslaan
        $path = $request->file('csv_file')->store('temp');
        $fullPath = storage_path('app/' . $path);
          try {
            // CSV-bestand lezen met native PHP functies
            $file = fopen($fullPath, 'r');
            
            // Lees de headers
            $headers = fgetcsv($file);
            
            $records = [];
            $row = 0;
            
            // Lees alle rijen
            while (($data = fgetcsv($file)) !== FALSE) {
                $rowData = [];
                for ($i = 0; $i < count($headers); $i++) {
                    if (isset($data[$i])) {
                        $rowData[$headers[$i]] = $data[$i];
                    }
                }
                $records[] = $rowData;
            }
            
            fclose($file);
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
              // Verwerk elke rij
            foreach ($records as $index => $record) {
                $rowNumber = $index + 2; // +2 omdat we 0-gebaseerd zijn en de header rij 1 is
                
                $validator = Validator::make($record, [
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'price' => 'required|numeric|min:0',
                    'condition' => 'required|string|in:nieuw,gebruikt,goed,redelijk,matig',
                    'category' => 'required|string|max:100',
                    'location' => 'nullable|string|max:100',
                    'is_rental' => 'boolean',
                    'rental_price_day' => 'nullable|numeric|min:0|required_if:is_rental,1',
                    'rental_price_week' => 'nullable|numeric|min:0',
                    'rental_price_month' => 'nullable|numeric|min:0',
                    'minimum_rental_days' => 'nullable|integer|min:1',
                    'rental_conditions' => 'nullable|string',
                    'rental_requires_deposit' => 'boolean',
                    'rental_deposit_amount' => 'nullable|numeric|min:0|required_if:rental_requires_deposit,1',
                    'rental_calculate_wear_and_tear' => 'boolean',
                    'rental_wear_and_tear_settings' => 'nullable|string|required_if:rental_calculate_wear_and_tear,1',
                    'is_accepting_bids' => 'boolean',
                    'min_bid_amount' => 'nullable|numeric|min:0|required_if:is_accepting_bids,1',
                    'images_urls' => 'nullable|string',
                ]);
                
                if ($validator->fails()) {
                    $errorCount++;
                    $errors["Rij {$rowNumber}"] = $validator->errors()->all();
                    continue;
                }
                
                try {
                    // Maak nieuwe advertentie aan
                    $advertisement = new Advertisement();
                    $advertisement->user_id = Auth::id();
                    $advertisement->title = $record['title'];
                    $advertisement->description = $record['description'];
                    $advertisement->price = $record['price'];
                    $advertisement->condition = $record['condition'];
                    $advertisement->category = $record['category'];
                    $advertisement->location = $record['location'] ?? null;
                    $advertisement->status = 'active';
                    $advertisement->purchase_status = 'available';
                    $advertisement->type = 'product'; // Standaard type
                    
                    // Verhuur instellingen
                    $advertisement->is_rental = filter_var($record['is_rental'] ?? false, FILTER_VALIDATE_BOOLEAN);
                    if ($advertisement->is_rental) {
                        $advertisement->rental_price_day = $record['rental_price_day'];
                        $advertisement->rental_price_week = $record['rental_price_week'] ?? null;
                        $advertisement->rental_price_month = $record['rental_price_month'] ?? null;
                        $advertisement->minimum_rental_days = $record['minimum_rental_days'] ?? 1;
                        $advertisement->rental_conditions = $record['rental_conditions'] ?? null;
                        
                        // Borg instellingen
                        $advertisement->rental_requires_deposit = filter_var($record['rental_requires_deposit'] ?? false, FILTER_VALIDATE_BOOLEAN);
                        if ($advertisement->rental_requires_deposit) {
                            $advertisement->rental_deposit_amount = $record['rental_deposit_amount'];
                        }
                        
                        // Slijtage instellingen
                        $advertisement->rental_calculate_wear_and_tear = filter_var($record['rental_calculate_wear_and_tear'] ?? false, FILTER_VALIDATE_BOOLEAN);
                        if ($advertisement->rental_calculate_wear_and_tear) {
                            $advertisement->rental_wear_and_tear_settings = $record['rental_wear_and_tear_settings'];
                        }
                    }
                    
                    // Biedinstellingen
                    $advertisement->is_accepting_bids = filter_var($record['is_accepting_bids'] ?? false, FILTER_VALIDATE_BOOLEAN);
                    if ($advertisement->is_accepting_bids) {
                        $advertisement->min_bid_amount = $record['min_bid_amount'];
                    }
                    
                    // Afbeeldingen verwerken
                    if (!empty($record['images_urls'])) {
                        $imageUrls = explode(',', $record['images_urls']);
                        $advertisement->images = array_map('trim', $imageUrls);
                    }
                    
                    $advertisement->save();
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors["Rij {$rowNumber}"] = [$e->getMessage()];
                }
            }
            
            // Verwijder tijdelijk bestand
            Storage::delete($path);
            
            // Toon resultaat
            if ($successCount > 0) {
                session()->flash('import_success', "Succesvol {$successCount} advertenties geïmporteerd.");
            }
            
            if ($errorCount > 0) {
                session()->flash('import_errors', $errors);
            }
            
            return redirect()->route('advertisements.import')
                ->with('import_stats', [
                    'success' => $successCount,
                    'error' => $errorCount,
                ]);
        } catch (\Exception $e) {
            // Verwijder tijdelijk bestand bij fout
            Storage::delete($path);
            
            return redirect()->route('advertisements.import')
                ->with('error', 'Er is een fout opgetreden bij het verwerken van het CSV-bestand: ' . $e->getMessage());
        }
    }
      /**
     * Download een sjabloon CSV-bestand voor import.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        // Maak een sjabloon CSV-bestand
        $headers = [
            'title', 'description', 'price', 'condition', 'category', 'location',
            'is_rental', 'rental_price_day', 'rental_price_week', 'rental_price_month',
            'minimum_rental_days', 'rental_conditions', 'rental_requires_deposit',
            'rental_deposit_amount', 'rental_calculate_wear_and_tear',
            'rental_wear_and_tear_settings', 'is_accepting_bids', 'min_bid_amount', 'images_urls'
        ];
        
        $path = storage_path('app/template.csv');
        $file = fopen($path, 'w');
        
        // Schrijf headers
        fputcsv($file, $headers);
        
        // Schrijf voorbeeldrij
        $exampleRow = [
            'title' => 'Voorbeeld product titel',
            'description' => 'Dit is een uitgebreide beschrijving van het product.',
            'price' => '99.95',
            'condition' => 'nieuw',
            'category' => 'Gereedschap',
            'location' => 'Amsterdam',
            'is_rental' => '0',
            'rental_price_day' => '',
            'rental_price_week' => '',
            'rental_price_month' => '',
            'minimum_rental_days' => '',
            'rental_conditions' => '',
            'rental_requires_deposit' => '0',
            'rental_deposit_amount' => '',
            'rental_calculate_wear_and_tear' => '0',
            'rental_wear_and_tear_settings' => '',
            'is_accepting_bids' => '0',
            'min_bid_amount' => '',
            'images_urls' => 'https://example.com/image1.jpg,https://example.com/image2.jpg'
        ];
        
        fputcsv($file, $exampleRow);
        
        // Voorbeeld verhuurrij
        $exampleRentalRow = [
            'title' => 'Verhuur: Kettingzaag',
            'description' => 'Professionele kettingzaag te huur.',
            'price' => '299.95',
            'condition' => 'goed',
            'category' => 'Gereedschap',
            'location' => 'Rotterdam',
            'is_rental' => '1',
            'rental_price_day' => '25',
            'rental_price_week' => '150',
            'rental_price_month' => '500',
            'minimum_rental_days' => '1',
            'rental_conditions' => 'Alleen voor professioneel gebruik. Legitimatie verplicht.',
            'rental_requires_deposit' => '1',
            'rental_deposit_amount' => '100',
            'rental_calculate_wear_and_tear' => '1',
            'rental_wear_and_tear_settings' => '{"amount":"5","type":"flat"}',
            'is_accepting_bids' => '0',
            'min_bid_amount' => '',
            'images_urls' => 'https://example.com/kettingzaag.jpg'
        ];
        
        fputcsv($file, $exampleRentalRow);
        
        fclose($file);
        
        return response()->download($path, 'advertenties_import_sjabloon.csv', [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }
}
