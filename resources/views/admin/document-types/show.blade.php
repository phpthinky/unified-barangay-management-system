@extends('layouts.barangay')

@section('title', $documentType->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-alt"></i> {{ $documentType->name }}</h2>
        <div>
            <a href="{{ route('barangay.document-types.edit', $documentType) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('barangay.document-types.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Left Column - Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Document Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name</th>
                            <td>{{ $documentType->name }}</td>
                        </tr>
                        <tr>
                            <th>Slug</th>
                            <td><code>{{ $documentType->slug }}</code></td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $documentType->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td><span class="badge bg-info">{{ ucfirst($documentType->category) }}</span></td>
                        </tr>
                        <tr>
                            <th>Document Format</th>
                            <td>
                                @if($documentType->document_format == 'certificate')
                                    <span class="badge bg-primary">📄 Certificate</span>
                                @elseif($documentType->document_format == 'id_card')
                                    <span class="badge bg-warning text-dark">🪪 ID Card</span>
                                @elseif($documentType->document_format == 'half_sheet')
                                    <span class="badge bg-secondary">📑 Half Sheet</span>
                                @elseif($documentType->document_format == 'legal')
                                    <span class="badge bg-dark">📋 Legal</span>
                                @else
                                    <span class="badge bg-secondary">⚙️ Custom</span>
                                @endif
                                <br>
                                <small class="text-muted">
                                    @if($documentType->document_format == 'certificate')
                                        Standard Certificate (8.5" x 11" or A4)
                                    @elseif($documentType->document_format == 'id_card')
                                        ID Card Size (3.375" x 2.125")
                                    @elseif($documentType->document_format == 'half_sheet')
                                        Half Sheet / Short Bond
                                    @elseif($documentType->document_format == 'legal')
                                        Legal Size (8.5" x 14")
                                    @else
                                        Custom Format
                                    @endif
                                </small>
                                @if($documentType->format_notes)
                                    <br><small class="text-info"><i class="fas fa-info-circle"></i> {{ $documentType->format_notes }}</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Fee</th>
                            <td><strong>₱{{ number_format($documentType->fee, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Processing Days</th>
                            <td>{{ $documentType->processing_days }} {{ $documentType->processing_days == 1 ? 'day' : 'days' }}</td>
                        </tr>
                        <tr>
                            <th>Sort Order</th>
                            <td>{{ $documentType->sort_order }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Requirements -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Requirements</h5>
                </div>
                <div class="card-body">
                    @php
                        $requirements = $documentType->requirements;
                        if (!is_array($requirements)) {
                            $requirements = [];
                        }
                    @endphp
                    @if(count($requirements) > 0)
                        <ul>
                            @foreach($requirements as $requirement)
                                <li>{{ $requirement }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No specific requirements</p>
                    @endif
                </div>
            </div>

            <!-- Form Fields -->
            @php
                $formFields = $documentType->form_fields;
                if (!is_array($formFields)) {
                    $formFields = [];
                }
            @endphp
            @if(count($formFields) > 0)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Form Fields ({{ count($formFields) }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Field Name</th>
                                    <th width="25%">Label</th>
                                    <th width="15%">Type</th>
                                    <th width="15%">Required</th>
                                    <th width="15%">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($formFields as $index => $field)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $field['name'] ?? '' }}</code></td>
                                    <td><strong>{{ $field['label'] ?? '' }}</strong></td>
                                    <td>
                                        @php
                                            $fieldType = $field['type'] ?? 'text';
                                        @endphp
                                        @if($fieldType == 'text')
                                            <span class="badge bg-primary"><i class="fas fa-font"></i> Text</span>
                                        @elseif($fieldType == 'textarea')
                                            <span class="badge bg-info"><i class="fas fa-align-left"></i> Textarea</span>
                                        @elseif($fieldType == 'number')
                                            <span class="badge bg-warning text-dark"><i class="fas fa-hashtag"></i> Number</span>
                                        @elseif($fieldType == 'date')
                                            <span class="badge bg-success"><i class="fas fa-calendar"></i> Date</span>
                                        @elseif($fieldType == 'select')
                                            <span class="badge bg-secondary"><i class="fas fa-list"></i> Select</span>
                                        @else
                                            <span class="badge bg-dark">{{ $fieldType }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($field['required']) && $field['required'])
                                            <span class="badge bg-danger"><i class="fas fa-asterisk"></i> Required</span>
                                        @else
                                            <span class="badge bg-secondary">Optional</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($field['options']) && is_array($field['options']))
                                            <small class="text-muted">
                                                @foreach($field['options'] as $option)
                                                    <span class="badge bg-light text-dark">{{ $option }}</span>
                                                @endforeach
                                            </small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Form Fields</h5>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No custom form fields configured for this document type.</p>
                    <p class="text-muted mb-0">
                        <small>Residents will only need to provide basic information auto-filled from their profile.</small>
                    </p>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Status & Stats -->
        <div class="col-md-4">
            <!-- Template Preview -->
            @if($documentType->template_content)
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-file-code"></i> Custom Template</h5>
                </div>
                <div class="card-body">
                    <p class="text-success mb-2"><i class="fas fa-check-circle"></i> <strong>Custom template active</strong></p>
                    <p class="text-muted mb-3"><small>This document type uses a custom certificate template.</small></p>
                    
                    <a href="{{ route('barangay.document-types.template', $documentType) }}" class="btn btn-warning btn-sm w-100 mb-2">
                        <i class="fas fa-edit"></i> Edit Template
                    </a>
                    
                    <details>
                        <summary class="btn btn-sm btn-secondary w-100">View Code</summary>
                        <pre class="mt-2 p-2 bg-light border" style="max-height: 250px; overflow-y: auto; font-size: 10px;">{{ $documentType->template_content }}</pre>
                    </details>
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-file-code"></i> Template</h5>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">Using default template</p>
                    <a href="{{ route('barangay.document-types.template', $documentType) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Create Custom Template
                    </a>
                </div>
            </div>
            @endif

            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Printing:</strong>
                        @if($documentType->enable_printing)
                            <span class="badge bg-success"><i class="fas fa-check"></i> Enabled</span>
                        @else
                            <span class="badge bg-danger"><i class="fas fa-times"></i> Disabled</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Verification Required:</strong>
                        @if($documentType->requires_verification)
                            <span class="badge bg-warning">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong>
                        @if($documentType->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Requests:</span>
                            <strong>{{ $stats['total_requests'] ?? 0 }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Pending:</span>
                            <span class="badge bg-warning">{{ $stats['pending_requests'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Approved:</span>
                            <span class="badge bg-success">{{ $stats['approved_requests'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Rejected:</span>
                            <span class="badge bg-danger">{{ $stats['rejected_requests'] ?? 0 }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between">
                            <span><strong>Total Revenue:</strong></span>
                            <strong class="text-success">₱{{ number_format($stats['total_revenue'] ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('barangay.document-types.toggleStatus', $documentType) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-{{ $documentType->is_active ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $documentType->is_active ? 'pause' : 'play' }}"></i> 
                            {{ $documentType->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <form action="{{ route('barangay.document-types.togglePrinting', $documentType) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-{{ $documentType->enable_printing ? 'danger' : 'info' }} w-100">
                            <i class="fas fa-{{ $documentType->enable_printing ? 'ban' : 'print' }}"></i> 
                            {{ $documentType->enable_printing ? 'Disable' : 'Enable' }} Printing
                        </button>
                    </form>

                    <a href="{{ route('barangay.document-types.edit', $documentType) }}" class="btn btn-sm btn-warning w-100 mb-2">
                        <i class="fas fa-edit"></i> Edit Details
                    </a>

                    <form action="{{ route('barangay.document-types.destroy', $documentType) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this document type?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger w-100" 
                                {{ ($stats['total_requests'] ?? 0) > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                    @if(($stats['total_requests'] ?? 0) > 0)
                        <small class="text-muted">Cannot delete - has existing requests</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Requests -->
    @if(isset($recentRequests) && $recentRequests->count() > 0)
    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Recent Requests</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tracking #</th>
                            <th>Resident</th>
                            <th>Barangay</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRequests as $request)
                        <tr>
                            <td><code>{{ $request->tracking_number }}</code></td>
                            <td>{{ $request->user->name ?? 'N/A' }}</td>
                            <td>{{ $request->barangay->name ?? 'N/A' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($request->purpose ?? 'N/A', 30) }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'pending' => 'warning'
                                    ];
                                    $color = $statusColors[$request->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ ucfirst($request->status) }}</span>
                            </td>
                            <td>{{ $request->submitted_at ? $request->submitted_at->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection