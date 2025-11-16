<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Barangay;
use App\Models\ResidentProfile;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        
        // Only residents need to complete profiles
        if (!$user->hasRole('resident')) {
            return redirect()->route('dashboard');
        }
        
        $profile = $user->profile ?? new ResidentProfile();
    
        return view('profile.edit', [
            'profile' => $profile,
            'completion' => $profile->exists ? $profile->completionPercentage() : 0,
            'barangays' => Barangay::orderBy('name')->get(),
            'idTypes' => [
                'Passport',
                'Driver\'s License',
                'SSS ID',
                'PhilHealth ID',
                'TIN ID',
                'Postal ID',
                'Voter\'s ID',
                'Senior Citizen ID',
                'UMID',
                'Other Government ID'
            ]
        ]);
    }

    public function update(ProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();
        
        // Handle file uploads
        if ($request->hasFile('valid_id_path')) {
            // Delete old file if exists
            if ($user->profile && $user->profile->valid_id_path) {
                Storage::delete($user->profile->valid_id_path);
            }
            $data['valid_id_path'] = $request->file('valid_id_path')->store('valid-ids');
        }
        
        if ($request->hasFile('proof_of_residency_path')) {
            // Delete old file if exists
            if ($user->profile && $user->profile->proof_of_residency_path) {
                Storage::delete($user->profile->proof_of_residency_path);
            }
            $data['proof_of_residency_path'] = $request->file('proof_of_residency_path')->store('proof-of-residency');
        }
        
        // Update or create profile (including barangay_id from the form)
        $profile = $user->profile()->updateOrCreate(['user_id' => $user->id], $data);
        
        // Determine redirect based on profile completion
        if ($profile->isComplete()) {
            // Upgrade from guest to resident if needed
            if ($user->hasRole('guest')) {
                $user->removeRole('guest');
                $user->assignRole('resident');
            }
            
            return redirect()->route('resident.dashboard')
                ->with('success', 'Profile completed! You now have full access to resident services.');
        }
        
        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully')
            ->with('warning', 'Please complete all required fields to access resident services');
    }
}