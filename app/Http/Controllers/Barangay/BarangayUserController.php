<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Barangay;
use Spatie\Permission\Models\Role;

class BarangayUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:barangay-captain|barangay-secretary']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            abort(403, 'No barangay assignment found.');
        }

        $query = User::where('barangay_id', $barangay->id)
                    ->with(['roles'])
                    ->whereHas('roles', function($roleQuery) {
                        $roleQuery->whereIn('name', [
                            'barangay-secretary',
                            'barangay-staff',
                            'lupon-member'
                        ]);
                    });

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true)->where('is_archived', false);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate(15)
                      ->appends($request->query());

        $roles = Role::whereIn('name', [
            'barangay-secretary',
            'barangay-staff',
            'lupon-member'
        ])->orderBy('name')->get();

        $stats = [
            'total_staff' => User::where('barangay_id', $barangay->id)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['barangay-secretary', 'barangay-staff', 'lupon-member']);
                })->count(),
            'active_staff' => User::where('barangay_id', $barangay->id)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['barangay-secretary', 'barangay-staff', 'lupon-member']);
                })
                ->where('is_active', true)
                ->where('is_archived', false)
                ->count(),
            'lupon_members' => User::where('barangay_id', $barangay->id)
                ->role('lupon-member')
                ->where('is_active', true)
                ->count(),
        ];

        return view('barangay.users.index', compact('users', 'roles', 'stats', 'barangay'));
    }

    public function create()
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            abort(403, 'No barangay assignment found.');
        }

        // Only allow captain to create certain roles
        $allowedRoles = ['barangay-secretary', 'barangay-staff', 'lupon-member'];

        // If user is secretary, restrict role creation
        if ($user->hasRole('barangay-secretary')) {
            $allowedRoles = ['barangay-staff', 'lupon-member'];
        }

        $roles = Role::whereIn('name', $allowedRoles)->orderBy('name')->get();
        
        $committees = [
            'peace_order' => 'Committee on Peace and Order',
            'health_sanitation' => 'Committee on Health and Sanitation',
            'education' => 'Committee on Education',
            'agriculture' => 'Committee on Agriculture',
            'infrastructure' => 'Committee on Infrastructure',
            'environment' => 'Committee on Environment',
            'budget_finance' => 'Committee on Budget and Finance',
            'women_family' => 'Committee on Women and Family',
            'youth_sports' => 'Committee on Youth and Sports',
            'senior_pwd' => 'Committee on Senior Citizens and PWD',
            'livelihood' => 'Committee on Livelihood',
            'tourism_culture' => 'Committee on Tourism and Culture',
        ];
        
        return view('barangay.users.create', compact('roles', 'committees', 'barangay'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            abort(403, 'No barangay assignment found.');
        }

        // Check role permission
        if ($user->hasRole('barangay-secretary') && !in_array($request->role, ['barangay-staff', 'lupon-member'])) {
            return redirect()->back()
                           ->withErrors(['role' => 'You are not authorized to create this role.'])
                           ->withInput();
        }

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'role' => 'required|exists:roles,name|in:barangay-councilor,barangay-secretary,barangay-treasurer,barangay-staff,lupon-member',
            'password' => 'required|string|min:8|confirmed',
            'term_start' => 'nullable|date',
            'term_end' => 'nullable|date|after:term_start',
            'position_title' => 'nullable|string|max:255',
        ];

        if ($request->role === 'barangay-councilor') {
            $rules['committee'] = 'required|string';
            $rules['councilor_number'] = 'required|integer|between:1,7';
        }

        $request->validate($rules);

        // Check for existing councilor with same number
        if ($request->role === 'barangay-councilor') {
            $existingCouncilor = User::where('barangay_id', $barangay->id)
                                    ->role('barangay-councilor')
                                    ->where('councilor_number', $request->councilor_number)
                                    ->where('is_active', true)
                                    ->where('is_archived', false)
                                    ->first();

            if ($existingCouncilor) {
                return redirect()->back()
                               ->withErrors(['councilor_number' => 'This councilor number is already assigned to another active councilor in your barangay.'])
                               ->withInput();
            }
        }

        // Check for existing secretary or treasurer
        if (in_array($request->role, ['barangay-secretary', 'barangay-treasurer'])) {
            $existingRole = User::where('barangay_id', $barangay->id)
                               ->role($request->role)
                               ->where('is_active', true)
                               ->where('is_archived', false)
                               ->first();

            if ($existingRole) {
                $roleTitle = ucwords(str_replace('-', ' ', $request->role));
                return redirect()->back()
                               ->withErrors(['role' => "Your barangay already has an active {$roleTitle}."])
                               ->withInput();
            }
        }

        $userData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'suffix' => $request->suffix,
            'name' => trim($request->first_name . ' ' . ($request->middle_name ? $request->middle_name . ' ' : '') . $request->last_name),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'barangay_id' => $barangay->id,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'position_title' => $request->position_title,
            'email_verified_at' => now(), // Auto-verify since created by barangay admin
        ];

        $termRoles = ['barangay-councilor', 'barangay-secretary'];
        if (in_array($request->role, $termRoles)) {
            $userData['term_start'] = $request->term_start ?? now();
            $userData['term_end'] = $request->term_end ?? now()->addYears(3);
        }

        if ($request->role === 'barangay-councilor') {
            $userData['committee'] = $request->committee;
            $userData['councilor_number'] = $request->councilor_number;
            if (!$request->position_title) {
                $userData['position_title'] = 'Barangay Councilor';
            }
        }

        $newUser = User::create($userData);
        $newUser->assignRole($request->role);

        return redirect()->route('barangay.users.show', $newUser)
                       ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $authUser = Auth::user();
        
        // Ensure user belongs to same barangay
        if ($user->barangay_id !== $authUser->barangay_id) {
            abort(403, 'Unauthorized access.');
        }

        $user->load(['roles', 'barangay']);
        
        $stats = [
            'documents_processed' => $user->processedDocuments()->count(),
            'complaints_handled' => $user->assignedComplaints()->count(),
            'permits_processed' => $user->processedPermits()->count(),
            'residents_verified' => $user->verifiedResidents()->count(),
        ];

        $recentActivity = [
            'documents' => $user->processedDocuments()->with('documentType')->latest()->take(5)->get(),
            'complaints' => $user->assignedComplaints()->with('complaintType', 'complainant')->latest()->take(5)->get(),
            'permits' => $user->processedPermits()->with('businessPermitType', 'applicant')->latest()->take(5)->get(),
        ];

        return view('barangay.users.show', compact('user', 'stats', 'recentActivity'));
    }

    public function edit(User $user)
    {
        $authUser = Auth::user();
        
        // Ensure user belongs to same barangay
        if ($user->barangay_id !== $authUser->barangay_id) {
            abort(403, 'Unauthorized access.');
        }

        // Secretary can only edit staff and lupon members
        if ($authUser->hasRole('barangay-secretary') && !$user->hasAnyRole(['barangay-staff', 'lupon-member'])) {
            abort(403, 'Unauthorized to edit this user.');
        }

        $allowedRoles = ['barangay-councilor', 'barangay-secretary', 'barangay-treasurer', 'barangay-staff', 'lupon-member'];
        
        if ($authUser->hasRole('barangay-secretary')) {
            $allowedRoles = ['barangay-staff', 'lupon-member'];
        }

        $roles = Role::whereIn('name', $allowedRoles)->orderBy('name')->get();
        
        $committees = [
            'peace_order' => 'Committee on Peace and Order',
            'health_sanitation' => 'Committee on Health and Sanitation',
            'education' => 'Committee on Education',
            'agriculture' => 'Committee on Agriculture',
            'infrastructure' => 'Committee on Infrastructure',
            'environment' => 'Committee on Environment',
            'budget_finance' => 'Committee on Budget and Finance',
            'women_family' => 'Committee on Women and Family',
            'youth_sports' => 'Committee on Youth and Sports',
            'senior_pwd' => 'Committee on Senior Citizens and PWD',
            'livelihood' => 'Committee on Livelihood',
            'tourism_culture' => 'Committee on Tourism and Culture',
        ];
        
        $termStart = $user->term_start ? $user->term_start->format('Y-m-d') : '';
        $termEnd = $user->term_end ? $user->term_end->format('Y-m-d') : '';
        
        return view('barangay.users.edit', compact('user', 'roles', 'committees', 'termStart', 'termEnd'));
    }

    public function update(Request $request, User $user)
    {
        $authUser = Auth::user();
        
        // Ensure user belongs to same barangay
        if ($user->barangay_id !== $authUser->barangay_id) {
            abort(403, 'Unauthorized access.');
        }

        // Secretary can only edit staff and lupon members
        if ($authUser->hasRole('barangay-secretary') && !$user->hasAnyRole(['barangay-staff', 'lupon-member'])) {
            abort(403, 'Unauthorized to edit this user.');
        }

        // Check role permission for secretaries
        if ($authUser->hasRole('barangay-secretary') && !in_array($request->role, ['barangay-staff', 'lupon-member'])) {
            return redirect()->back()
                           ->withErrors(['role' => 'You are not authorized to assign this role.'])
                           ->withInput();
        }

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'role' => 'required|exists:roles,name|in:barangay-councilor,barangay-secretary,barangay-treasurer,barangay-staff,lupon-member',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'nullable|boolean',
            'term_start' => 'nullable|date',
            'term_end' => 'nullable|date|after:term_start',
            'position_title' => 'nullable|string|max:255',
        ];

        if ($request->role === 'barangay-councilor') {
            $rules['committee'] = 'required|string';
            $rules['councilor_number'] = 'required|integer|between:1,7';
        }

        $request->validate($rules);

        $currentRole = $user->getRoleNames()->first();
        
        // Check for existing councilor with same number
        if ($request->role === 'barangay-councilor') {
            $existingCouncilor = User::where('barangay_id', $authUser->barangay_id)
                                    ->role('barangay-councilor')
                                    ->where('councilor_number', $request->councilor_number)
                                    ->where('is_active', true)
                                    ->where('is_archived', false)
                                    ->where('id', '!=', $user->id)
                                    ->first();

            if ($existingCouncilor) {
                return redirect()->back()
                               ->withErrors(['councilor_number' => 'This councilor number is already assigned to another active councilor in your barangay.'])
                               ->withInput();
            }
        }

        // Check for existing secretary or treasurer when changing roles
        if ($request->role !== $currentRole && in_array($request->role, ['barangay-secretary', 'barangay-treasurer'])) {
            $existingRole = User::where('barangay_id', $authUser->barangay_id)
                               ->role($request->role)
                               ->where('is_active', true)
                               ->where('is_archived', false)
                               ->where('id', '!=', $user->id)
                               ->first();

            if ($existingRole) {
                $roleTitle = ucwords(str_replace('-', ' ', $request->role));
                return redirect()->back()
                               ->withErrors(['role' => "Your barangay already has an active {$roleTitle}."])
                               ->withInput();
            }
        }

        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'suffix' => $request->suffix,
            'name' => trim($request->first_name . ' ' . ($request->middle_name ? $request->middle_name . ' ' : '') . $request->last_name),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'is_active' => $request->boolean('is_active', true),
            'position_title' => $request->position_title,
        ];

        $termRoles = ['barangay-councilor', 'barangay-secretary'];
        if (in_array($request->role, $termRoles)) {
            $updateData['term_start'] = $request->term_start;
            $updateData['term_end'] = $request->term_end;
        } else {
            $updateData['term_start'] = null;
            $updateData['term_end'] = null;
        }

        if ($request->role === 'barangay-councilor') {
            $updateData['committee'] = $request->committee;
            $updateData['councilor_number'] = $request->councilor_number;
            if (!$request->position_title) {
                $updateData['position_title'] = 'Barangay Councilor';
            }
        } else {
            $updateData['committee'] = null;
            $updateData['councilor_number'] = null;
        }

        $user->update($updateData);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($request->role !== $currentRole) {
            $user->syncRoles([$request->role]);
        }

        return redirect()->route('barangay.users.show', $user)
                       ->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        $authUser = Auth::user();
        
        // Ensure user belongs to same barangay
        if ($user->barangay_id !== $authUser->barangay_id) {
            abort(403, 'Unauthorized access.');
        }

        // Secretary can only toggle staff and lupon members
        if ($authUser->hasRole('barangay-secretary') && !$user->hasAnyRole(['barangay-staff', 'lupon-member'])) {
            abort(403, 'Unauthorized to modify this user.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
                       ->with('success', "User {$status} successfully.");
    }

    public function destroy(User $user)
    {
        $authUser = Auth::user();
        
        // Only captain can delete
        if (!$authUser->hasRole('barangay-captain')) {
            abort(403, 'Only barangay captain can delete users.');
        }

        // Ensure user belongs to same barangay
        if ($user->barangay_id !== $authUser->barangay_id) {
            abort(403, 'Unauthorized access.');
        }

        // Cannot delete captain or other high-level roles
        if ($user->hasAnyRole(['barangay-captain', 'municipality-admin', 'abc-president'])) {
            return redirect()->back()
                           ->with('error', 'Cannot delete this user role.');
        }

        $user->delete();

        return redirect()->route('barangay.users.index')
                       ->with('success', 'User deleted successfully.');
    }
}