<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangayOfficial;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangayOfficialController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:abc-president']);
    }

    /**
     * Display a listing of barangay officials.
     */
    public function index(Request $request)
    {
        $query = BarangayOfficial::with('barangay');

        // Filter by barangay
        if ($request->filled('barangay_id')) {
            $query->where('barangay_id', $request->barangay_id);
        }

        // Filter by term
        if ($request->filled('term')) {
            if ($request->term === 'current') {
                $query->where('is_active', true)
                      ->where('term_start', '<=', now())
                      ->where('term_end', '>=', now());
            } elseif ($request->term === 'past') {
                $query->where('term_end', '<', now());
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('position', 'like', '%' . $request->search . '%')
                  ->orWhere('committee', 'like', '%' . $request->search . '%');
            });
        }

        $officials = $query->ordered()->paginate(15)->appends($request->query());
        $barangays = Barangay::where('is_active', true)->orderBy('name')->get();

        return view('admin.barangay-officials.index', compact('officials', 'barangays'));
    }

    /**
     * Show the form for creating a new official.
     */
    public function create()
    {
        $barangays = Barangay::where('is_active', true)->orderBy('name')->get();
        return view('admin.barangay-officials.create', compact('barangays'));
    }

    /**
     * Store a newly created official in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barangay_id' => 'required|exists:barangays,id',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'committee' => 'nullable|string|max:255',
            'display_order' => 'required|integer|min:0|max:999',
            'term_start' => 'required|date',
            'term_end' => 'required|date|after:term_start',
            'is_active' => 'boolean',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string|max:1000',
        ]);

        // Handle checkbox
        $validated['is_active'] = $request->has('is_active');

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('officials', 'public');
        }

        BarangayOfficial::create($validated);

        return redirect()->route('admin.barangay-officials.index')
                       ->with('success', 'Official added successfully.');
    }

    /**
     * Display the specified official.
     */
    public function show(BarangayOfficial $barangayOfficial)
    {
        $barangayOfficial->load('barangay');
        return view('admin.barangay-officials.show', compact('barangayOfficial'));
    }

    /**
     * Show the form for editing the specified official.
     */
    public function edit(BarangayOfficial $barangayOfficial)
    {
        $barangays = Barangay::where('is_active', true)->orderBy('name')->get();
        return view('admin.barangay-officials.edit', compact('barangayOfficial', 'barangays'));
    }

    /**
     * Update the specified official in storage.
     */
    public function update(Request $request, BarangayOfficial $barangayOfficial)
    {
        $validated = $request->validate([
            'barangay_id' => 'required|exists:barangays,id',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'committee' => 'nullable|string|max:255',
            'display_order' => 'required|integer|min:0|max:999',
            'term_start' => 'required|date',
            'term_end' => 'required|date|after:term_start',
            'is_active' => 'boolean',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string|max:1000',
        ]);

        // Handle checkbox
        $validated['is_active'] = $request->has('is_active');

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($barangayOfficial->avatar) {
                Storage::disk('public')->delete($barangayOfficial->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('officials', 'public');
        }

        $barangayOfficial->update($validated);

        return redirect()->route('admin.barangay-officials.index')
                       ->with('success', 'Official updated successfully.');
    }

    /**
     * Remove the specified official from storage.
     */
    public function destroy(BarangayOfficial $barangayOfficial)
    {
        // Delete avatar if exists
        if ($barangayOfficial->avatar) {
            Storage::disk('public')->delete($barangayOfficial->avatar);
        }

        $barangayOfficial->delete();

        return redirect()->route('admin.barangay-officials.index')
                       ->with('success', 'Official deleted successfully.');
    }
}
