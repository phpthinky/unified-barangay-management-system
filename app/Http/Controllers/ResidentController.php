<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ResidentController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $profileComplete = $user->profile && $user->profile->isComplete();
        
        return view('resident.dashboard', [
            'profileComplete' => $profileComplete,
            'pendingRequests' => $profileComplete ? $user->documentRequests()->where('status', 'pending')->count() : 0,
            'activeComplaints' => $profileComplete ? $user->complaints()->where('status', '!=', 'resolved')->count() : 0
        ]);
    }
}