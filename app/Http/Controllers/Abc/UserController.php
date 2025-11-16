<?php

namespace App\Http\Controllers\Abc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Barangay;
use App\Models\Term;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $query = User::with(['roles', 'barangay']);

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('barangay_id')) {
            $query->where('barangay_id', $request->barangay_id);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true)->where('is_archived', false);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'archived':
                    $query->where('is_archived', true);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('barangay', function($barangayQuery) use ($search) {
                      $barangayQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!$request->boolean('include_residents')) {
            $query->whereDoesntHave('roles', function($roleQuery) {
                $roleQuery->where('name', 'resident');
            });
        }

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate(25)
                      ->appends($request->query());

        $barangays = Barangay::orderBy('name')->get();
        $roles = Role::where('name', '!=', 'resident')->orderBy('name')->get();

        $stats = [
            'total_officials' => User::whereHas('roles', function($q) {
                $q->whereIn('name', ['municipality-admin', 'abc-president', 'barangay-captain', 'barangay-councilor', 'barangay-secretary', 'barangay-treasurer', 'barangay-staff', 'lupon-member']);
            })->count(),
            'active_officials' => User::whereHas('roles', function($q) {
                $q->whereIn('name', ['municipality-admin', 'abc-president', 'barangay-captain', 'barangay-councilor', 'barangay-secretary', 'barangay-treasurer', 'barangay-staff', 'lupon-member']);
            })->where('is_active', true)->where('is_archived', false)->count(),
            'archived_officials' => User::where('is_archived', true)->count(),
            'pending_activation' => User::where('is_active', false)->where('is_archived', false)->count(),
        ];

        return view('abc.users.index', compact('users', 'barangays', 'roles', 'stats'));
    }

    public function create()
    {
        $barangays = Barangay::active()->orderBy('name')->get();
        $roles = Role::whereIn('name', [
            'municipality-admin', 
            'abc-president', 
            'barangay-captain', 
            'barangay-councilor',
            'barangay-secretary',
            'barangay-staff', 
            'lupon-member'
        ])->orderBy('name')->get();
        
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
        
        return view('abc.users.create', compact('barangays', 'roles', 'committees'));
    }

    public function store(Request $request)
    {
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
            'barangay_id' => 'nullable|exists:barangays,id',
            'role' => 'required|exists:roles,name',
            'password' => 'required|string|min:8|confirmed',
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

        $barangayRoles = ['barangay-captain', 'barangay-councilor', 'barangay-secretary', 'barangay-treasurer', 'barangay-staff', 'lupon-member'];
        if (in_array($request->role, $barangayRoles) && !$request->barangay_id) {
            return redirect()->back()
                           ->withErrors(['barangay_id' => 'Barangay assignment is required for this role.'])
                           ->withInput();
        }

        if ($request->role === 'barangay-captain' && $request->barangay_id) {
            $existingCaptain = User::where('barangay_id', $request->barangay_id)
                                 ->role('barangay-captain')
                                 ->where('is_active', true)
                                 ->where('is_archived', false)
                                 ->first();

            if ($existingCaptain) {
                return redirect()->back()
                               ->withErrors(['role' => 'This barangay already has an active captain.'])
                               ->withInput();
            }
        }

        if ($request->role === 'barangay-councilor' && $request->barangay_id) {
            $existingCouncilor = User::where('barangay_id', $request->barangay_id)
                                    ->role('barangay-councilor')
                                    ->where('councilor_number', $request->councilor_number)
                                    ->where('is_active', true)
                                    ->where('is_archived', false)
                                    ->first();

            if ($existingCouncilor) {
                return redirect()->back()
                               ->withErrors(['councilor_number' => 'This councilor number is already assigned to another active councilor in this barangay.'])
                               ->withInput();
            }
        }

        if ($request->role === 'abc-president') {
            $existingPresident = User::role('abc-president')
                                   ->where('is_active', true)
                                   ->where('is_archived', false)
                                   ->first();

            if ($existingPresident) {
                return redirect()->back()
                               ->withErrors(['role' => 'There is already an active ABC President.'])
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
            'barangay_id' => $request->barangay_id,
            'password' => Hash::make($request->password),
            'is_active' => $request->boolean('is_active', true),
            'position_title' => $request->position_title,
        ];

        $termRoles = ['abc-president', 'barangay-captain', 'barangay-councilor', 'barangay-secretary'];
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

        $user = User::create($userData);
        $user->assignRole($request->role);

        return redirect()->route('abc.users.show', $user)
                       ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
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

        return view('abc.users.show', compact('user', 'stats', 'recentActivity'));
    }

    public function edit(User $user)
    {
        $barangays = Barangay::active()->orderBy('name')->get();
        $roles = Role::whereIn('name', [
            'municipality-admin', 
            'abc-president', 
            'barangay-captain', 
            'barangay-councilor',
            'barangay-secretary',
            'barangay-treasurer',
            'barangay-staff', 
            'lupon-member'
        ])->orderBy('name')->get();
        
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
        
        return view('abc.users.edit', compact('user', 'barangays', 'roles', 'committees', 'termStart', 'termEnd'));
    }

    public function update(Request $request, User $user)
    {
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
            'barangay_id' => 'nullable|exists:barangays,id',
            'role' => 'required|exists:roles,name',
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

        $barangayRoles = ['barangay-captain', 'barangay-councilor', 'barangay-secretary', 'barangay-treasurer', 'barangay-staff', 'lupon-member'];
        if (in_array($request->role, $barangayRoles) && !$request->barangay_id) {
            return redirect()->back()
                           ->withErrors(['barangay_id' => 'Barangay assignment is required for this role.'])
                           ->withInput();
        }

        $currentRole = $user->getRoleNames()->first();
        if ($request->role !== $currentRole) {
            if ($request->role === 'barangay-captain' && $request->barangay_id) {
                $existingCaptain = User::where('barangay_id', $request->barangay_id)
                                     ->role('barangay-captain')
                                     ->where('is_active', true)
                                     ->where('is_archived', false)
                                     ->where('id', '!=', $user->id)
                                     ->first();

                if ($existingCaptain) {
                    return redirect()->back()
                                   ->withErrors(['role' => 'This barangay already has an active captain.'])
                                   ->withInput();
                }
            }

            if ($request->role === 'abc-president') {
                $existingPresident = User::role('abc-president')
                                       ->where('is_active', true)
                                       ->where('is_archived', false)
                                       ->where('id', '!=', $user->id)
                                       ->first();

                if ($existingPresident) {
                    return redirect()->back()
                                   ->withErrors(['role' => 'There is already an active ABC President.'])
                                   ->withInput();
                }
            }
        }

        if ($request->role === 'barangay-councilor' && $request->barangay_id) {
            $existingCouncilor = User::where('barangay_id', $request->barangay_id)
                                    ->role('barangay-councilor')
                                    ->where('councilor_number', $request->councilor_number)
                                    ->where('is_active', true)
                                    ->where('is_archived', false)
                                    ->where('id', '!=', $user->id)
                                    ->first();

            if ($existingCouncilor) {
                return redirect()->back()
                               ->withErrors(['councilor_number' => 'This councilor number is already assigned to another active councilor in this barangay.'])
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
            'barangay_id' => $request->barangay_id,
            'is_active' => $request->boolean('is_active', true),
            'position_title' => $request->position_title,
        ];

        $termRoles = ['abc-president', 'barangay-captain', 'barangay-councilor', 'barangay-secretary'];
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

        return redirect()->route('abc.users.show', $user)
                       ->with('success', 'User updated successfully.');
    }

    public function archive(User $user)
    {
        if ($user->hasRole('municipality-admin')) {
            return redirect()->back()
                           ->with('error', 'Cannot archive municipality admin users.');
        }

        $user->archive('Archived by admin');

        return redirect()->back()
                       ->with('success', 'User archived successfully.');
    }

    public function restore(User $user)
    {
        $user->update([
            'is_active' => true,
            'is_archived' => false,
            'deleted_at' => null,
        ]);

        return redirect()->back()
                       ->with('success', 'User restored successfully.');
    }

    public function bulkArchive(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'archive_reason' => 'required|string|in:term_ended,election_change,resignation,other',
            'archive_notes' => 'nullable|string|max:500'
        ]);

        $archivedCount = 0;
        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);
            
            if ($user->hasRole('municipality-admin')) {
                continue;
            }

            $user->archive($request->archive_reason . ': ' . $request->archive_notes);
            $archivedCount++;
        }

        return redirect()->back()
                       ->with('success', "Successfully archived {$archivedCount} users.");
    }

    public function exportExcel(Request $request)
    {
        $query = User::with(['roles', 'barangay']);

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('barangay_id')) {
            $query->where('barangay_id', $request->barangay_id);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true)->where('is_archived', false);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'archived':
                    $query->where('is_archived', true);
                    break;
            }
        }

        if (!$request->boolean('include_residents')) {
            $query->whereDoesntHave('roles', function($roleQuery) {
                $roleQuery->where('name', 'resident');
            });
        }

        $users = $query->get();

        $filename = 'ubms_users_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new UsersExport($users), $filename);
    }

    public function getByBarangay(Request $request, Barangay $barangay)
    {
        $role = $request->get('role');
        
        $query = $barangay->users()->where('is_active', true)->where('is_archived', false);
        
        if ($role) {
            $query->role($role);
        }

        $users = $query->with('roles')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
                'position_title' => $user->position_title,
            ];
        });

        return response()->json($users);
    }

    public function toggleStatus(User $user)
    {
        if ($user->hasRole('municipality-admin')) {
            return redirect()->back()
                           ->with('error', 'Cannot deactivate municipality admin users.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
                       ->with('success', "User {$status} successfully.");
    }
}