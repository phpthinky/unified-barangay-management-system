<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Models\User;
use Illuminate\Support\Str;

class BarangayController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $query = Barangay::withCount([
            'users as total_users',
            'residentProfiles as total_residents',
            'verifiedResidents as verified_residents',
            'documentRequests as document_requests',
            'complaints as complaints',
            'businessPermits as business_permits'
        ]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $barangays = $query->orderBy('name')->paginate(10)->appends($request->query());

        return view('admin.barangays.index', compact('barangays'));
    }

    public function create()
    {
        return view('admin.barangays.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:barangays',
            'slug' => 'nullable|string|max:255|unique:barangays',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'social_media' => 'nullable|array',
        ]);

        $data = $request->all();

        if (!$data['slug']) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'logo_' . Str::slug($data['name']) . '_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('uploads/logos'), $logoName);
            $data['logo'] = $logoName;
        }

        $barangay = Barangay::create($data);
        
        // Generate QR Code automatically
        $this->generateQrCode($barangay);

        return redirect()->route('admin.barangays.index')
                       ->with('success', 'Barangay created successfully.');
    }

    public function show(Barangay $barangay)
    {
        $barangay->load(['users.roles', 'residentProfiles.user']);
        
        $stats = [
            'officials' => $barangay->users()->whereHas('roles', function($query) {
                $query->whereIn('name', ['barangay-captain', 'barangay-secretary', 'barangay-staff']);
            })->count(),
            'lupon_members' => $barangay->users()->whereHas('roles', function($query) {
                $query->where('name', 'lupon');
            })->count(),
            'total_residents' => $barangay->residentProfiles()->count(),
            'verified_residents' => $barangay->verifiedResidents()->count(),
            'pending_residents' => $barangay->pendingResidents()->count(),
            'document_requests' => $barangay->documentRequests()->count(),
            'pending_documents' => $barangay->documentRequests()->where('status', 'pending')->count(),
            'complaints' => $barangay->complaints()->count(),
            'active_complaints' => $barangay->complaints()->whereIn('status', ['received', 'assigned', 'in_process'])->count(),
            'business_permits' => $barangay->businessPermits()->count(),
            'active_permits' => $barangay->businessPermits()->where('status', 'approved')->where('expires_at', '>', now())->count(),
        ];

        return view('admin.barangays.show', compact('barangay', 'stats'));
    }

    public function edit(Barangay $barangay)
    {
        return view('admin.barangays.edit', compact('barangay'));
    }

    public function update(Request $request, Barangay $barangay)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:barangays,name,' . $barangay->id,
            'slug' => 'nullable|string|max:255|unique:barangays,slug,' . $barangay->id,
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'social_media' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($barangay->logo && file_exists(public_path('uploads/logos/' . $barangay->logo))) {
                unlink(public_path('uploads/logos/' . $barangay->logo));
            }

            $logo = $request->file('logo');
            $logoName = 'logo_' . Str::slug($data['name']) . '_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('uploads/logos'), $logoName);
            $data['logo'] = $logoName;
        }

        $barangay->update($data);

        return redirect()->route('admin.barangays.show', $barangay)
                       ->with('success', 'Barangay updated successfully.');
    }

    public function destroy(Barangay $barangay)
    {
        if ($barangay->users()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete barangay with existing users.');
        }

        // Delete logo file
        if ($barangay->logo && file_exists(public_path('uploads/logos/' . $barangay->logo))) {
            unlink(public_path('uploads/logos/' . $barangay->logo));
        }

        // Delete QR code
        if ($barangay->qr_code && file_exists(public_path('uploads/qr-codes/' . $barangay->qr_code))) {
            unlink(public_path('uploads/qr-codes/' . $barangay->qr_code));
        }

        $barangay->delete();

        return redirect()->route('admin.barangays.index')
                       ->with('success', 'Barangay deleted successfully.');
    }

    public function generateQr(Barangay $barangay)
    {
        $this->generateQrCode($barangay);

        return redirect()->back()
                       ->with('success', 'QR Code generated successfully.');
    }

    /**
     * Generate QR Code using Endroid QR Code v6.0 (uses GD, no ImageMagick needed!)
     */
    private function generateQrCode(Barangay $barangay)
    {
        try {
            // Ensure directory exists (Windows compatible)
            $qrPath = public_path('uploads' . DIRECTORY_SEPARATOR . 'qr-codes');
            if (!file_exists($qrPath)) {
                mkdir($qrPath, 0777, true);
                \Log::info('Created QR codes directory: ' . $qrPath);
            }

            $qrCodeName = 'qr_' . $barangay->slug . '_' . time() . '.png';
            $qrCodePath = $qrPath . DIRECTORY_SEPARATOR . $qrCodeName;
            
            // Delete old QR code if exists
            if ($barangay->qr_code) {
                $oldQrPath = public_path('uploads' . DIRECTORY_SEPARATOR . 'qr-codes' . DIRECTORY_SEPARATOR . $barangay->qr_code);
                if (file_exists($oldQrPath)) {
                    unlink($oldQrPath);
                    \Log::info('Deleted old QR code: ' . $barangay->qr_code);
                }
            }

            // Use helper method
            $success = \App\Helpers\QrCodeHelper::saveToFile(
                $barangay->registration_url,
                $qrCodePath,
                300
            );

            if (!$success) {
                throw new \Exception('QR code file was not created at: ' . $qrCodePath);
            }

            // Update barangay record
            $barangay->update(['qr_code' => $qrCodeName]);

            $fileSize = filesize($qrCodePath);
            \Log::info('QR Code generated successfully for: ' . $barangay->name . ' (' . $fileSize . ' bytes)');
            
            return true;

        } catch (\Exception $e) {
            \Log::error('QR Code generation failed for ' . $barangay->name . ': ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Fallback to external API if Endroid fails
            return $this->generateQrCodeFallback($barangay);
        }
    }

    /**
     * Fallback method using external API
     */
    private function generateQrCodeFallback(Barangay $barangay)
    {
        try {
            $qrPath = public_path('uploads' . DIRECTORY_SEPARATOR . 'qr-codes');
            $qrCodeName = 'qr_' . $barangay->slug . '_' . time() . '.png';
            $qrCodePath = $qrPath . DIRECTORY_SEPARATOR . $qrCodeName;
            
            $url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($barangay->registration_url);
            $imageData = file_get_contents($url);
            
            if ($imageData) {
                file_put_contents($qrCodePath, $imageData);
                $barangay->update(['qr_code' => $qrCodeName]);
                \Log::info('QR Code generated via fallback API for: ' . $barangay->name);
                return true;
            }
            
            \Log::error('Fallback API returned no data');
            return false;
        } catch (\Exception $e) {
            \Log::error('Fallback QR generation also failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Display QR inline (for views)
     */
    public function getQrCodeInline(Barangay $barangay)
    {
        try {
            $dataUri = \App\Helpers\QrCodeHelper::generate($barangay->registration_url, 300);
            
            if (empty($dataUri)) {
                abort(500, 'QR Code generation failed');
            }
            
            // Extract the base64 part and decode it
            $base64Data = explode(',', $dataUri)[1];
            $imageData = base64_decode($base64Data);
            
            return response($imageData)
                ->header('Content-Type', 'image/png');
        } catch (\Exception $e) {
            abort(500, 'QR Code generation failed: ' . $e->getMessage());
        }
    }
}