{{-- FILE: resources/views/admin/document-types/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Document Type Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>{{ $documentType->name }}</h4>
                <div>
                    <a href="{{ route('admin.document-types.edit', $documentType) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.document-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Name:</th>
                        <td>{{ $documentType->name }}</td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td>{{ $documentType->description ?: 'No description' }}</td>
                    </tr>
                    <tr>
                        <th>Fee:</th>
                        <td>{{ $documentType->formatted_fee }}</td>
                    </tr>
                    <tr>
                        <th>Processing Time:</th>
                        <td>{{ $documentType->processing_time_text }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge bg-{{ $documentType->is_active ? 'success' : 'danger' }}">
                                {{ $documentType->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>File Upload:</th>
                        <td>
                            {{ $documentType->requires_file_upload ? 'Required' : 'Not required' }}
                            @if($documentType->requires_file_upload && $documentType->file_upload_label)
                                <br><small class="text-muted">{{ $documentType->file_upload_label }}</small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>QR Code:</th>
                        <td>{{ $documentType->qr_enabled ? 'Enabled' : 'Disabled' }}</td>
                    </tr>
                </table>

                @if($documentType->requirements && count($documentType->requirements) > 0)
                <div class="mt-4">
                    <h5>Requirements:</h5>
                    <ul>
                        @foreach($documentType->requirements as $requirement)
                            <li>{{ $requirement }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($documentType->template_content)
                <div class="mt-4">
                    <h5>Template Content:</h5>
                    <div class="border p-3 bg-light">
                        <pre>{{ $documentType->template_content }}</pre>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Statistics Card -->
        <div class="card">
            <div class="card-header">
                <h5>Request Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <h3 class="text-primary">{{ $stats['total_requests'] }}</h3>
                        <small class="text-muted">Total Requests</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning">{{ $stats['pending_requests'] }}</h4>
                        <small class="text-muted">Pending</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $stats['approved_requests'] }}</h4>
                        <small class="text-muted">Approved</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
