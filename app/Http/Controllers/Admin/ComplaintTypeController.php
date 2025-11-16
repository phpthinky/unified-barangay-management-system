<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplaintType;

class ComplaintTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $query = ComplaintType::withCount('complaints');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $complaintTypes = $query->orderBy('sort_order')
                               ->orderBy('name')
                               ->paginate(15)
                               ->appends($request->query());

        $categories = ComplaintType::distinct()->pluck('category')->filter();

        return view('admin.complaint-types.index', compact('complaintTypes', 'categories'));
    }

    public function create()
    {
        return view('admin.complaint-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:complaint_types',
            'slug' => 'nullable|string|max:255|unique:complaint_types',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:civil,criminal,administrative,barangay,others',
            'default_handler_type' => 'required|in:captain,secretary,lupon,any_staff',
            'requires_hearing' => 'boolean',
            'estimated_resolution_days' => 'required|integer|min:1|max:365',
            'required_information' => 'nullable|array',
            'required_information.*' => 'string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        ComplaintType::create($request->all());

        return redirect()->route('admin.complaint-types.index')
                       ->with('success', 'Complaint type created successfully.');
    }

    public function show(ComplaintType $complaintType)
    {
        $complaintType->load('complaints.complainant');
        
        $stats = [
            'total_complaints' => $complaintType->complaints()->count(),
            'active_complaints' => $complaintType->complaints()->whereIn('status', ['received', 'assigned', 'in_process'])->count(),
            'resolved_complaints' => $complaintType->complaints()->where('status', 'resolved')->count(),
            'dismissed_complaints' => $complaintType->complaints()->where('status', 'dismissed')->count(),
            'avg_resolution_days' => $this->getAverageResolutionDays($complaintType),
        ];

        $recentComplaints = $complaintType->complaints()
                                        ->with(['complainant', 'barangay'])
                                        ->latest()
                                        ->take(10)
                                        ->get();

        return view('admin.complaint-types.show', compact('complaintType', 'stats', 'recentComplaints'));
    }

    public function edit(ComplaintType $complaintType)
    {
        return view('admin.complaint-types.edit', compact('complaintType'));
    }

    public function update(Request $request, ComplaintType $complaintType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:complaint_types,name,' . $complaintType->id,
            'slug' => 'nullable|string|max:255|unique:complaint_types,slug,' . $complaintType->id,
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:civil,criminal,administrative,barangay,others',
            'default_handler_type' => 'required|in:captain,secretary,lupon,any_staff',
            'requires_hearing' => 'boolean',
            'estimated_resolution_days' => 'required|integer|min:1|max:365',
            'required_information' => 'nullable|array',
            'required_information.*' => 'string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $complaintType->update($request->all());

        return redirect()->route('admin.complaint-types.show', $complaintType)
                       ->with('success', 'Complaint type updated successfully.');
    }

    public function destroy(ComplaintType $complaintType)
    {
        if ($complaintType->complaints()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete complaint type with existing complaints.');
        }

        $complaintType->delete();

        return redirect()->route('admin.complaint-types.index')
                       ->with('success', 'Complaint type deleted successfully.');
    }

    private function getAverageResolutionDays(ComplaintType $complaintType)
    {
        $resolvedComplaints = $complaintType->complaints()
                                          ->whereNotNull('resolved_at')
                                          ->get();

        if ($resolvedComplaints->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        foreach ($resolvedComplaints as $complaint) {
            $totalDays += $complaint->received_at->diffInDays($complaint->resolved_at);
        }

        return round($totalDays / $resolvedComplaints->count(), 1);
    }
}