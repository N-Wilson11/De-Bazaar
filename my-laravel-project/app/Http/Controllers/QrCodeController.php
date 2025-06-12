<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

class QrCodeController extends Controller
{
    /**
     * Generate QR code for an advertisement
     *
     * @param  Request  $request
     * @param  Advertisement  $advertisement
     * @return \Illuminate\Http\Response
     */    public function show(Request $request, Advertisement $advertisement)
    {
        $size = $request->query('size', 200);
        $download = $request->boolean('download', false);
        $url = route('advertisements.show', $advertisement->id);
        $qrCodePath = "qrcodes/advertisement-{$advertisement->id}.png";
        
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            // If GD is not available and download is requested, we'll redirect to an external QR code service
            if ($download) {
                return redirect("https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($url) . "&download=1");
            }
            
            // For regular display, inline the QR code from the external service
            return response()->redirectTo("https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($url));
        }
        
        // Generate QR code if it doesn't exist or is requested with a custom size
        try {
            if (!Storage::disk('public')->exists($qrCodePath) || $request->has('size')) {
                // In version 6.0 of the Endroid QR Code library, we use the constructor parameters
                $builder = new \Endroid\QrCode\Builder\Builder(
                    writer: new PngWriter(),
                    writerOptions: [],
                    validateResult: false,
                    data: $url,
                    encoding: new Encoding('UTF-8'),
                    errorCorrectionLevel: ErrorCorrectionLevel::Medium,
                    size: $size,
                    margin: 10,
                    roundBlockSizeMode: RoundBlockSizeMode::Margin,
                    foregroundColor: new Color(0, 0, 0),
                    backgroundColor: new Color(255, 255, 255)
                );
                
                $result = $builder->build();
                
                // Make sure the directory exists
                Storage::disk('public')->makeDirectory('qrcodes');
                
                // Save the QR code
                Storage::disk('public')->put($qrCodePath, $result->getString());
            }
        } catch (\Exception $e) {
            // If there's any error generating the QR code, use the external service
            if ($download) {
                return redirect("https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($url) . "&download=1");
            }
            
            return response()->redirectTo("https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($url));
        }
        
        // If download is requested, return as an attachment
        if ($download) {
            $headers = [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="advertisement-' . $advertisement->id . '-qrcode.png"',
            ];
            
            return Response::download(Storage::disk('public')->path($qrCodePath), "advertisement-{$advertisement->id}-qrcode.png", $headers);
        }
        
        // Otherwise, return as an inline image
        return response()->file(Storage::disk('public')->path($qrCodePath), [
            'Content-Type' => 'image/png',
        ]);
    }
}
