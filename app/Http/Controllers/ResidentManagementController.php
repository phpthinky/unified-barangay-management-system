<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResidentProfile;
use Illuminate\Http\Request;

class ResidentManagementController extends Controller
{
    /**
     * Display a listing of all residents (for admin/staff/captain).
     */
    public function index(Request $request)
    {
        $query = ResidentProfile::query();

        // Optional search filter
        if ($request->has('search') && $request->search !== null) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $residents = $query->latest()->paginate(10);

        return view('residents.index', compact('residents'));
    }

    /**
     * Show the form for creating a new resident (by admin/staff).
     */
    public function create()
    {
        return view('residents.create');
    }

    /**
     * Store a newly created resident in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:resident_profiles,email',
            'address'    => 'nullable|string|max:500',
        ]);

        ResidentProfile::create($request->all());

        return redirect()->route('residents.index')->with('success', 'Resident profile created successfully.');
    }

    /**
     * Display the specified resident.
     */
    public function show(ResidentProfile $resident)
    {
        return view('residents.show', compact('resident'));
    }

    /**
     * Show the form for editing the specified resident.
     */
    public function edit(ResidentProfile $resident)
    {
        return view('residents.edit', compact('resident'));
    }

    /**
     * Update the specified resident in storage.
     */
    public function update(Request $request, ResidentProfile $resident)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:resident_profiles,email,' . $resident->id,
            'address'    => 'nullable|string|max:500',
        ]);

        $resident->update($request->all());

        return redirect()->route('residents.index')->with('success', 'Resident profile updated successfully.');
    }

    /**
     * Remove the specified resident from storage.
     */
    public function destroy(ResidentProfile $resident)
    {
        $resident->delete();

        return redirect()->route('residents.index')->with('success', 'Resident profile deleted successfully.');
    }
}
