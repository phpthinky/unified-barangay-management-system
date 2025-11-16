<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::where('user_id', auth()->id())
            ->with('barangay')
            ->latest()
            ->paginate(10);
            
        return view('complaints.index', compact('complaints'));
    }

    public function create()
    {
        return view('complaints.create', [
            'barangays' => Barangay::orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barangay_id' => 'required|exists:barangays,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:noise_disturbance,property_dispute,sanitation,public_safety,violence,other',
            'location' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('complaints/photos', 'public');
        }

        Complaint::create([
            'user_id' => auth()->id(),
            'barangay_id' => $validated['barangay_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'location' => $validated['location'],
            'landmark' => $validated['landmark'],
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint submitted successfully!');
    }

    public function show(Complaint $complaint)
    {
        $this->authorize('view', $complaint);
        
        return view('complaints.show', compact('complaint'));
    }
}