@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Complaint Details</h3>
                <span class="badge 
                    {{ $complaint->status == 'pending' ? 'bg-warning' : '' }}
                    {{ $complaint->status == 'under_investigation' ? 'bg-info' : '' }}
                    {{ $complaint->status == 'resolved' ? 'bg-success' : '' }}
                    {{ $complaint->status == 'dismissed' ? 'bg-secondary' : '' }}">
                    {{ Str::headline($complaint->status) }}
                </span>
            </div>
            <p class="text-muted mb-0">Complaint #{{ $complaint->id }}</p>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Complaint Information</h5>
                    <dl class="row">
                        <dt class="col-sm-4">Type:</dt>
                        <dd class="col-sm-8">{{ $complaint->type_name }}</dd>
                        
                        <dt class="col-sm-4">Date Filed:</dt>
                        <dd class="col-sm-8">{{ $complaint->created_at->format('F j, Y h:i A') }}</dd>
                        
                        <dt class="col-sm-4">Location:</dt>
                        <dd class="col-sm-8">{{ $complaint->location }}</dd>
                        
                        @if($complaint->landmark)
                        <dt class="col-sm-4">Landmark:</dt>
                        <dd class="col-sm-8">{{ $complaint->landmark }}</dd>
                        @endif
                        
                        <dt class="col-sm-4">Barangay:</dt>
                        <dd class="col-sm-8">{{ $complaint->barangay->name }}</dd>
                    </dl>
                </div>
                
                @if($complaint->photo_path)
                <div class="col-md-6">
                    <h5>Photo Evidence</h5>
                    <img src="{{ Storage::url($complaint->photo_path) }}" 
                         alt="Complaint photo" 
                         class="img-fluid rounded border"
                         style="max-height: 200px;">
                </div>
                @endif
            </div>
            
            <div class="mb-4">
                <h5>Complaint Description</h5>
                <div class="border p-3 rounded bg-light">
                    {{ $complaint->description }}
                </div>
            </div>
            
            @if($complaint->status == 'resolved' || $complaint->status == 'dismissed')
            <div class="mb-4">
                <h5>Resolution</h5>
                <div class="border p-3 rounded bg-light">
                    {{ $complaint->resolution }}
                </div>
                <div class="mt-2 text-muted small">
                    Resolved on: {{ $complaint->resolved_at?->format('F j, Y') }}
                    @if($complaint->resolver)
                        by {{ $complaint->resolver->name }}
                    @endif
                </div>
            </div>
            @endif
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left me-2"></i> Back to List
                </a>
                @if($complaint->status == 'pending')
                <button class="btn btn-outline-danger">
                    <i class="bi bi-trash me-2"></i> Withdraw Complaint
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection