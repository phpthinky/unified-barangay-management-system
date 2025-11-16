<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ComplaintHearing;

class LuponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display list of lupon members
     */
    public function index()
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            return redirect()->route('dashboard')->with('error', 'No barangay assigned.');
        }

        // Get all lupon members for this barangay
        $luponMembers = User::where('barangay_id', $barangay->id)
                           ->role('lupon') // FIXED: was 'lupon-member'
                           ->with('roles')
                           ->withCount([
                               'assignedComplaints',
                               'assignedComplaints as active_complaints_count' => function($query) {
                                   $query->active();
                               }
                           ])
                           ->orderBy('is_active', 'desc')
                           ->orderBy('first_name')
                           ->paginate(20);

        // Add hearing counts manually
        foreach ($luponMembers as $member) {
            $member->presiding_hearings_count = ComplaintHearing::where('presiding_officer', $member->id)->count();
            $member->completed_hearings_count = ComplaintHearing::where('presiding_officer', $member->id)
                                                                ->where('status', 'completed')
                                                                ->count();
        }

        // Stats
        $stats = [
            'total' => User::where('barangay_id', $barangay->id)->role('lupon')->count(),
            'active' => User::where('barangay_id', $barangay->id)->role('lupon')->where('is_active', true)->count(),
            'inactive' => User::where('barangay_id', $barangay->id)->role('lupon')->where('is_active', false)->count(),
        ];

        return view('barangay.lupon.index', compact('barangay', 'luponMembers', 'stats'));
    }

    /**
     * Show lupon member details
     */
    public function show(User $user)
    {
        $authUser = Auth::user();
        
        // Check if user is from same barangay and is a lupon member
        if ($user->barangay_id != $authUser->barangay_id || !$user->hasRole('lupon')) {
            abort(403, 'Unauthorized access.');
        }

        $user->load([
            'assignedComplaints' => function($query) {
                $query->latest()->take(10);
            },
            'assignedComplaints.complaintType',
            'assignedComplaints.complainant'
        ]);

        // Get hearings presided by this lupon member
        $presidingHearings = ComplaintHearing::where('presiding_officer', $user->id)
                                            ->with('complaint')
                                            ->latest()
                                            ->take(10)
                                            ->get();

        return view('barangay.lupon.show', compact('user', 'presidingHearings'));
    }
}