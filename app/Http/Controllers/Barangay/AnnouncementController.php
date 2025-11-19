<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'ubms.barangay']);
        $this->middleware('role:barangay-captain|barangay-secretary');
    }

    /**
     * Display a listing of announcements.
     */
    public function index(Request $request)
    {
        $barangay = auth()->user()->barangay;

        $query = Announcement::where('barangay_id', $barangay->id)
                             ->with('createdBy');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->ordered()->paginate(15)->appends($request->query());

        return view('barangay.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        return view('barangay.announcements.create');
    }

    /**
     * Store a newly created announcement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'status' => 'required|in:draft,published',
            'pin_to_top' => 'boolean',
            'show_on_public' => 'boolean',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
        ]);

        $validated['barangay_id'] = auth()->user()->barangay_id;
        $validated['created_by'] = auth()->id();
        $validated['pin_to_top'] = $request->has('pin_to_top');
        $validated['show_on_public'] = $request->has('show_on_public');

        // Auto-set published_at if status is published and not set
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $announcement = Announcement::create($validated);

        return redirect()->route('barangay.announcements.show', $announcement)
                       ->with('success', 'Announcement created successfully.');
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);

        $announcement->load('createdBy', 'barangay');

        return view('barangay.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        return view('barangay.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'status' => 'required|in:draft,published,archived',
            'pin_to_top' => 'boolean',
            'show_on_public' => 'boolean',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
        ]);

        $validated['pin_to_top'] = $request->has('pin_to_top');
        $validated['show_on_public'] = $request->has('show_on_public');

        // Auto-set published_at if status changed to published
        if ($validated['status'] === 'published' && !$announcement->published_at && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $announcement->update($validated);

        return redirect()->route('barangay.announcements.show', $announcement)
                       ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified announcement.
     */
    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);

        $announcement->delete();

        return redirect()->route('barangay.announcements.index')
                       ->with('success', 'Announcement deleted successfully.');
    }

    /**
     * Publish the announcement.
     */
    public function publish(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $announcement->update([
            'status' => 'published',
            'published_at' => $announcement->published_at ?? now(),
        ]);

        return redirect()->back()
                       ->with('success', 'Announcement published successfully.');
    }

    /**
     * Archive the announcement.
     */
    public function archive(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $announcement->update(['status' => 'archived']);

        return redirect()->back()
                       ->with('success', 'Announcement archived successfully.');
    }

    /**
     * Toggle pin status.
     */
    public function togglePin(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $announcement->update(['pin_to_top' => !$announcement->pin_to_top]);

        $status = $announcement->pin_to_top ? 'pinned' : 'unpinned';

        return redirect()->back()
                       ->with('success', "Announcement {$status} successfully.");
    }
}
