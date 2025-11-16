@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Document Request #{{ $documentRequest->control_number }}</h3>
                <a href="{{ route('requests.show', ['documentRequest' => $documentRequest->id, 'download' => true]) }}" 
                   class="btn btn-light">
                   <i class="fas fa-download"></i> Download Receipt
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Resident Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5><i class="fas fa-user"></i> Resident Information</h5>
                    <p>{{ $resident->first_name }} {{ $resident->last_name }}</p>
                    <p>{{ $resident->house_number }} {{ $resident->street }}, Purok {{ $resident->purok }}</p>
                </div>
                <div class="col-md-6">
                    <h5><i class="fas fa-file-alt"></i> Document Details</h5>
                    <p><strong>Type:</strong> {{ ucfirst($documentRequest->type) }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $documentRequest->status === 'approved' ? 'success' : ($documentRequest->status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($documentRequest->status) }}
                        </span>
                    </p>
                    <p><strong>Date:</strong> {{ $dateIssued }}</p>
                </div>
            </div>

            <!-- PDF Preview (Mirrors the downloadable version) -->
            <div class="border p-4 bg-white">
                <h5 class="text-center mb-4">Receipt Preview</h5>
                <div class="d-flex justify-content-center">
                    <div style="width: 100%; max-width: 600px;">
                        @include('requests.document-receipt', [
                            'documentRequest' => $documentRequest,
                            'qrCodePng' => $qrCodePng,
                            'dateIssued' => $dateIssued,
                            'barangay' => $barangay,
                            'resident' => $resident,
                            'isPreview' => true
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection