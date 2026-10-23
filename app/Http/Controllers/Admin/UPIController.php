<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UPIController extends Controller
{
    /**
     * Constructor - Apply admin middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display UPI settings page
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Get current admin's settings
            $adminId = Auth::id();
            $setting = AdminSetting::where('admin_id', $adminId)->first();
            
            // If no setting exists, create empty object for the form
            if (!$setting) {
                $setting = new AdminSetting();
            }
            
            return view('admin.upi.index', compact('setting'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load UPI settings: ' . $e->getMessage());
        }
    }

    /**
     * Store or Update UPI ID and generate QR code
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'upi_id' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+$/i'
                ],
            ], [
                'upi_id.required' => 'UPI ID is required',
                'upi_id.regex' => 'Please enter a valid UPI ID (e.g., username@upi, username@okicici, username@paytm)',
            ]);

            $adminId = Auth::id();
            $upiId = trim($request->upi_id);
            
            // Generate QR code
            $qrCodeData = $this->generateQRCode($upiId);
            
            // Create or update admin setting
            AdminSetting::updateOrCreate(
                ['admin_id' => $adminId],
                [
                    'upi_id' => $upiId,
                    'qr_code_path' => $qrCodeData['path'],
                    'qr_code_url' => $qrCodeData['url'],
                ]
            );
            
            return redirect()->route('admin.upi.index')
                ->with('success', 'UPI ID saved and QR code generated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save UPI settings: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate QR code for existing UPI
     * @return \Illuminate\Http\RedirectResponse
     */
    public function regenerateQR()
    {
        try {
            $adminId = Auth::id();
            $setting = AdminSetting::where('admin_id', $adminId)->first();
            
            if (!$setting) {
                return redirect()->back()->with('error', 'No UPI settings found. Please set UPI ID first.');
            }
            
            if (empty($setting->upi_id)) {
                return redirect()->back()->with('error', 'No UPI ID found. Please set UPI ID first.');
            }
            
            // Delete old QR code if exists
            if ($setting->qr_code_path) {
                $oldPath = storage_path('app/' . $setting->qr_code_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Generate new QR code
            $qrCodeData = $this->generateQRCode($setting->upi_id);
            
            // Update QR code paths
            $setting->update([
                'qr_code_path' => $qrCodeData['path'],
                'qr_code_url' => $qrCodeData['url'],
            ]);
            
            return redirect()->route('admin.upi.index')
                ->with('success', 'QR code regenerated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to regenerate QR code: ' . $e->getMessage());
        }
    }

    /**
     * Delete UPI settings (reset)
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $adminId = Auth::id();
            $setting = AdminSetting::where('id', $id)
                ->where('admin_id', $adminId)
                ->first();
            
            if (!$setting) {
                return redirect()->back()->with('error', 'Settings not found.');
            }
            
            // Delete QR code file
            if ($setting->qr_code_path) {
                $filePath = storage_path('app/' . $setting->qr_code_path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            // Delete setting
            $setting->delete();
            
            return redirect()->route('admin.upi.index')
                ->with('success', 'UPI settings reset successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reset UPI settings: ' . $e->getMessage());
        }
    }

    /**
     * Download QR code image
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function downloadQR()
    {
        try {
            $adminId = Auth::id();
            $setting = AdminSetting::where('admin_id', $adminId)->first();
            
            if (!$setting) {
                return redirect()->back()->with('error', 'No UPI settings found.');
            }
            
            if (!$setting->qr_code_path) {
                return redirect()->back()->with('error', 'QR code not found.');
            }
            
            $filePath = storage_path('app/' . $setting->qr_code_path);
            
            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'QR code file not found.');
            }
            
            $fileName = 'sharpedge-upi-qr-' . date('Y-m-d') . '.png';
            
            return response()->download($filePath, $fileName);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to download QR code: ' . $e->getMessage());
        }
    }

    /**
     * View QR code in full size
     * @return \Illuminate\View\View
     */
    public function viewQR()
    {
        try {
            $adminId = Auth::id();
            $setting = AdminSetting::where('admin_id', $adminId)->first();
            
            if (!$setting || !$setting->qr_code_url) {
                return redirect()->route('admin.upi.index')
                    ->with('error', 'No QR code found. Please generate one first.');
            }
            
            return view('admin.upi.view-qr', compact('setting'));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to load QR code: ' . $e->getMessage());
        }
    }

    /**
     * Get current UPI settings via API (for AJAX calls)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUPISettings()
    {
        try {
            $adminId = Auth::id();
            $setting = AdminSetting::where('admin_id', $adminId)->first();
            
            return response()->json([
                'success' => true,
                'data' => $setting,
                'has_upi' => $setting ? true : false,
                'qr_url' => $setting ? $setting->qr_code_url : null
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate QR Code for UPI ID using Google Charts API (No external package needed)
     * @param string $upiId
     * @return array
     */
    private function generateQRCode($upiId)
    {
        // UPI Payment URL format (standard UPI payment link)
        $paymentString = "upi://pay?pa={$upiId}&pn=SharpEdge%20Services&mc=5499&tid=" . Str::random(12);
        
        // URL encode the payment string
        $encodedString = urlencode($paymentString);
        
        // Use QR Code Monkey API (most reliable)
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data={$encodedString}&format=png&margin=2&ecc=H";
        
        // Try to fetch the image
        $qrCodeImage = $this->fetchImageContent($qrCodeUrl);
        
        if (!$qrCodeImage) {
            // Fallback to Google Charts API
            $fallbackUrl = "https://chart.googleapis.com/chart?chs=400x400&cht=qr&chl={$encodedString}&choe=UTF-8&chld=H|2";
            $qrCodeImage = $this->fetchImageContent($fallbackUrl);
        }
        
        if (!$qrCodeImage) {
            throw new \Exception('Failed to generate QR code. Please check your internet connection and try again.');
        }
        
        // Create directory if not exists
        $directory = 'public/qr-codes';
        $fullDirectory = storage_path('app/' . $directory);
        
        if (!file_exists($fullDirectory)) {
            mkdir($fullDirectory, 0755, true);
        }
        
        // Generate unique filename
        $filename = 'qr-' . Str::slug($upiId) . '-' . time() . '.png';
        $path = $directory . '/' . $filename;
        $fullPath = $fullDirectory . '/' . $filename;
        
        // Save QR code to storage
        file_put_contents($fullPath, $qrCodeImage);
        
        // Ensure file permissions
        chmod($fullPath, 0644);
        
        // Return URL that points to storage link
        $url = asset('storage/qr-codes/' . $filename);
        
        return [
            'path' => $path, // Store as 'public/qr-codes/filename.png'
            'url' => $url    // Public URL via storage link
        ];
    }

    /**
     * Fetch image content from URL using multiple methods
     * @param string $url
     * @return string|bool
     */
    private function fetchImageContent($url)
    {
        // Method 1: Try with file_get_contents if allow_url_fopen is enabled
        if (ini_get('allow_url_fopen')) {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
                'http' => [
                    'timeout' => 30,
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
                ]
            ]);
            
            $image = @file_get_contents($url, false, $context);
            if ($image !== false) {
                return $image;
            }
        }
        
        // Method 2: Try with cURL if available
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ]);
            
            $image = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode == 200 && !empty($image)) {
                return $image;
            }
        }
        
        return false;
    }
}