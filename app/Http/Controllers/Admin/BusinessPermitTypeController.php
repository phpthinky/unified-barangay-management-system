<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessPermitType;

class BusinessPermitTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $query = BusinessPermitType::withCount('businessPermits');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $permitTypes = $query->orderBy('sort_order')
                            ->orderBy('name')
                            ->paginate(15)
                            ->appends($request->query());

        $categories = BusinessPermitType::distinct()->pluck('category')->filter();

        return view('admin.business-permit-types.index', compact('permitTypes', 'categories'));
    }

    public function create()
    {
        return view('admin.business-permit-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:business_permit_types',
            'slug' => 'nullable|string|max:255|unique:business_permit_types',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:micro,small,medium,large,home_based,street_vendor',
            'base_fee' => 'required|numeric|min:0|max:999999.99',
            'additional_fees' => 'nullable|array',
            'processing_days' => 'required|integer|min:1|max:365',
            'validity_months' => 'required|integer|min:1|max:60',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:255',
            'template_content' => 'nullable|string',
            'template_fields' => 'nullable|array',
            'template_fields.*' => 'string|max:100',
            'requires_inspection' => 'boolean',
            'requires_fire_safety' => 'boolean',
            'requires_health_permit' => 'boolean',
            'requires_environmental_clearance' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        BusinessPermitType::create($request->all());

        return redirect()->route('admin.business-permit-types.index')
                       ->with('success', 'Business permit type created successfully.');
    }

    public function show(BusinessPermitType $businessPermitType)
    {
        $businessPermitType->load('businessPermits.applicant');
        
        $stats = [
            'total_permits' => $businessPermitType->businessPermits()->count(),
            'pending_permits' => $businessPermitType->businessPermits()->where('status', 'pending')->count(),
            'approved_permits' => $businessPermitType->businessPermits()->where('status', 'approved')->count(),
            'rejected_permits' => $businessPermitType->businessPermits()->where('status', 'rejected')->count(),
            'active_permits' => $businessPermitType->businessPermits()->where('status', 'approved')->where('expires_at', '>', now())->count(),
            'expired_permits' => $businessPermitType->businessPermits()->where('expires_at', '<=', now())->count(),
            'total_revenue' => $businessPermitType->businessPermits()->sum('total_fees'),
        ];

        $recentPermits = $businessPermitType->businessPermits()
                                          ->with(['applicant', 'barangay'])
                                          ->latest()
                                          ->take(10)
                                          ->get();

        return view('admin.business-permit-types.show', compact('businessPermitType', 'stats', 'recentPermits'));
    }

    public function edit(BusinessPermitType $businessPermitType)
    {
        return view('admin.business-permit-types.edit', compact('businessPermitType'));
    }

    public function update(Request $request, BusinessPermitType $businessPermitType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:business_permit_types,name,' . $businessPermitType->id,
            'slug' => 'nullable|string|max:255|unique:business_permit_types,slug,' . $businessPermitType->id,
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:micro,small,medium,large,home_based,street_vendor',
            'base_fee' => 'required|numeric|min:0|max:999999.99',
            'additional_fees' => 'nullable|array',
            'processing_days' => 'required|integer|min:1|max:365',
            'validity_months' => 'required|integer|min:1|max:60',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:255',
            'template_content' => 'nullable|string',
            'template_fields' => 'nullable|array',
            'template_fields.*' => 'string|max:100',
            'requires_inspection' => 'boolean',
            'requires_fire_safety' => 'boolean',
            'requires_health_permit' => 'boolean',
            'requires_environmental_clearance' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $businessPermitType->update($request->all());

        return redirect()->route('admin.business-permit-types.show', $businessPermitType)
                       ->with('success', 'Business permit type updated successfully.');
    }

    public function destroy(BusinessPermitType $businessPermitType)
    {
        if ($businessPermitType->businessPermits()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete permit type with existing permits.');
        }

        $businessPermitType->delete();

        return redirect()->route('admin.business-permit-types.index')
                       ->with('success', 'Business permit type deleted successfully.');
    }
}