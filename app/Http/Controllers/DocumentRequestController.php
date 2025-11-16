<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\ResidentProfile;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf; // Add this at the top

class DocumentRequestController extends Controller
{

    public function index($value='')
    {
        // code...
    }
   public function create()
{
    $user = auth()->user();
    $profile = $user->residentProfile;

    if (!$profile || !$profile->isComplete()) {
        return redirect()->route('profile.edit')
            ->with('warning', 'Please complete your resident profile before requesting documents.');
    }

    // Get the barangay ID from the profile
    $barangayId = $profile->barangay_id;

    return view('requests.create', [
        'barangayId' => $barangayId,
        'barangayName' => $profile->barangay->name ?? 'Your Barangay',
        'fullAddress' => $profile->full_address
    ]);
}

    public function store(Request $request)
    {
        $user = auth()->user();
        $profile = $user->residentProfile;
  

        // Recheck profile completion (in case someone bypasses the UI)
        if (!$profile || !$profile->isComplete()) {
            abort(403, 'Complete your profile first');
        }

        $validated = $request->validate([
            'type' => 'required|in:clearance,indigency,permit,residency,good_moral,cedula',
            'purpose' => 'required|string|max:255',
            'additional_notes' => 'nullable|string',
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'file|max:5120|mimes:pdf,jpg,jpeg,png',
        ]);
        $control_number= $this->generateControlNumber();

        $documentRequest = DocumentRequest::create([
            'user_id' => $user->id,
            'resident_profile_id' => $profile->id,
            'barangay_id' => $profile->barangay_id, // Assuming you've added this to resident_profiles
            'type' => $validated['type'],
            'purpose' => $validated['purpose'],
            'additional_notes' => $validated['additional_notes'],
            'control_number' =>$control_number,
            'qr_code' => $control_number,
            'status' => 'pending'
        ]);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $documentRequest->addMedia($file)
                    ->toMediaCollection('attachments');
            }
        }

        return redirect()->route('requests.show', $documentRequest)
            ->with('success', 'Document request submitted successfully!');
    }

public function show(DocumentRequest $documentRequest)
{
    // Generate QR code content
    $qrContent = "Document Request\n";
    $qrContent .= "Type: " . ucfirst(str_replace('_', ' ', $documentRequest->type)) . "\n";
    $qrContent .= "Control #: {$documentRequest->control_number}\n";
    $qrContent .= "Resident: {$documentRequest->user->name}\n";
    $qrContent .= "Barangay: {$documentRequest->barangay->name}";

    return view('requests.show', [
        'request' => $documentRequest,
        'qrContent' => $qrContent,
        'qrCode' => QrCode::size(200)->generate($qrContent)
    ]);
}

public function downloadQr(DocumentRequest $documentRequest)
{
    $qrContent = "Document Request\n";
    $qrContent .= "Type: " . ucfirst(str_replace('_', ' ', $documentRequest->type)) . "\n";
    $qrContent .= "Control #: {$documentRequest->control_number}\n";
    $qrContent .= "Resident: {$documentRequest->user->name}\n";
    $qrContent .= "Barangay: {$documentRequest->barangay->name}";

    $qrCode = QrCode::format('png')
                ->size(300)
                ->generate($qrContent);

    return response($qrCode)
        ->header('Content-Type', 'image/png')
        ->header('Content-Disposition', 'attachment; filename="document-request-'.$documentRequest->control_number.'.png"');
}

    protected function formatAddress(ResidentProfile $profile)
    {
        return implode(', ', array_filter([
            $profile->house_number,
            $profile->street,
            $profile->purok ? 'Purok '.$profile->purok : null,
            $profile->barangay,
            $profile->municipality,
            $profile->province,
            $profile->zipcode
        ]));
    }

    protected function generateControlNumber()
    {
        return 'DOC-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
    }

    protected function generateQrCode()
    {
        return QrCode::format('svg')
            ->size(200)
            ->generate(Str::random(40));
    }
}