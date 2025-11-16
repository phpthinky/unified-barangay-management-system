@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Complaints</h2>
        <a href="{{ route('complaints.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> File New Complaint
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($complaints->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-chat-square-text text-muted" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">No complaints filed yet</h4>
                    <p class="text-muted">File your first complaint to get started</p>
                    <a href="{{ route('complaints.create') }}" class="btn btn-primary mt-2">
                        File a Complaint
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Complaint #</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Barangay</th>
                                <th>Status</th>
                                <th>Date Filed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($complaints as $complaint)
                            <tr>
                                <td>{{ $complaint->id }}</td>
                                <td>{{ Str::limit($complaint->title, 30) }}</td>
                                <td>{{ $complaint->type_name }}</td>
                                <td>{{ $complaint->barangay->name }}</td>
                                <td>
                                    <span class="badge 
                                        {{ $complaint->status == 'pending' ? 'bg-warning' : '' }}
                                        {{ $complaint->status == 'under_investigation' ? 'bg-info' : '' }}
                                        {{ $complaint->status == 'resolved' ? 'bg-success' : '' }}
                                        {{ $complaint->status == 'dismissed' ? 'bg-secondary' : '' }}">
                                        {{ Str::headline($complaint->status) }}
                                    </span>
                                </td>
                                <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('complaints.show', $complaint) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection