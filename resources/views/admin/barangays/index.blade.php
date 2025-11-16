@extends('layouts.admin')

@section('title', 'Barangay Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Barangay Management</h1>
            <p class="mb-0 text-muted">Manage all barangays in the municipality</p>
        </div>
        <a href="{{ route('admin.barangays.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Barangay
        </a>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.barangays.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Search Barangays</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search by name...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary mr-2">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('admin.barangays.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Barangays List Card -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Barangays List</h6>
        </div>
        <div class="card-body">
            @if($barangays->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>QR Code</th>
                                <th>Statistics</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barangays as $barangay)
                            <tr>
                                <td class="text-center">
                                    @if($barangay->logo)
                                        <img src="{{ asset('uploads/logos/' . $barangay->logo) }}" 
                                             alt="{{ $barangay->name }}" class="rounded-circle" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                             style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($barangay->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $barangay->name }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        <span class="badge badge-info">{{ $barangay->slug }}</span>
                                    </small>
                                    @if($barangay->address)
                                        <br><small class="text-muted">{{ Str::limit($barangay->address, 40) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($barangay->contact_number)
                                        <small><i class="fas fa-phone text-muted"></i> {{ $barangay->contact_number }}</small><br>
                                    @endif
                                    @if($barangay->email)
                                        <small><i class="fas fa-envelope text-muted"></i> {{ $barangay->email }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($barangay->qr_code && file_exists(public_path('uploads/qr-codes/' . $barangay->qr_code)))
                                        <!-- QR exists - show thumbnail with actions -->
                                        <div class="d-flex flex-column align-items-center">
                                            <img src="{{ asset('uploads/qr-codes/' . $barangay->qr_code) }}" 
                                                 alt="QR Code" 
                                                 class="img-thumbnail mb-2 qr-thumbnail"
                                                 style="width: 60px; height: 60px; cursor: pointer;"
                                                 onclick="viewQrCode({{ $barangay->id }}, '{{ addslashes($barangay->name) }}', '{{ asset('uploads/qr-codes/' . $barangay->qr_code) }}', '{{ $barangay->registration_url }}')">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary btn-sm" 
                                                        onclick="viewQrCode({{ $barangay->id }}, '{{ addslashes($barangay->name) }}', '{{ asset('uploads/qr-codes/' . $barangay->qr_code) }}', '{{ $barangay->registration_url }}')"
                                                        title="View QR">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="{{ asset('uploads/qr-codes/' . $barangay->qr_code) }}" 
                                                   download="qr_{{ $barangay->slug }}.png"
                                                   class="btn btn-outline-success btn-sm"
                                                   title="Download QR">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button class="btn btn-outline-secondary btn-sm" 
                                                        onclick="regenerateQr({{ $barangay->id }})"
                                                        title="Regenerate QR">
                                                    <i class="fas fa-sync"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <!-- QR doesn't exist - show generate button -->
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="generateQr({{ $barangay->id }})"
                                                id="generateBtn{{ $barangay->id }}">
                                            <i class="fas fa-qrcode"></i> Generate
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        <strong>Users:</strong> {{ number_format($barangay->total_users) }}<br>
                                        <strong>Residents:</strong> {{ number_format($barangay->total_residents) }} 
                                        ({{ number_format($barangay->verified_residents) }} verified)<br>
                                        <strong>Documents:</strong> {{ number_format($barangay->document_requests) }}<br>
                                        <strong>Complaints:</strong> {{ number_format($barangay->complaints) }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    @if($barangay->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                        <a href="{{ route('admin.barangays.show', $barangay) }}" 
                                           class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('admin.barangays.edit', $barangay) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete({{ $barangay->id }})" title="Delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $barangays->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                    <h5>No Barangays Found</h5>
                    <p class="text-muted">Start by creating your first barangay.</p>
                    <a href="{{ route('admin.barangays.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create First Barangay
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- QR Code View Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode"></i> <span id="qrModalTitle">QR Code</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="qrModalBody">
                <!-- QR Code will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="printQrCode()">
                    <i class="fas fa-print"></i> Print
                </button>
                <a id="downloadQrBtn" href="#" download class="btn btn-success">
                    <i class="fas fa-download"></i> Download
                </a>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this barangay? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> Deleting a barangay will also remove all associated data.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Barangay</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.qr-thumbnail:hover {
    transform: scale(1.05);
    transition: transform 0.2s;
}

@media print {
    body * {
        visibility: hidden;
    }
    #printableQr, #printableQr * {
        visibility: visible;
    }
    #printableQr {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>

<script>
// Define functions in global scope
window.currentQrData = {};

window.confirmDelete = function(barangayId) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/barangays/${barangayId}`;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

window.generateQr = function(barangayId) {
    const btn = document.getElementById(`generateBtn${barangayId}`);
    const originalHtml = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    
    fetch(`/admin/barangays/${barangayId}/generate-qr`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', 'QR Code generated successfully!');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', data.message || 'Error generating QR code');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Error generating QR code');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

window.regenerateQr = function(barangayId) {
    if (!confirm('Are you sure you want to regenerate the QR code? The old QR code will be replaced.')) {
        return;
    }
    generateQr(barangayId);
}

window.viewQrCode = function(barangayId, barangayName, qrImageUrl, registrationUrl) {
    window.currentQrData = {
        id: barangayId,
        name: barangayName,
        imageUrl: qrImageUrl,
        registrationUrl: registrationUrl
    };
    
    document.getElementById('qrModalTitle').textContent = barangayName + ' - QR Code';
    document.getElementById('downloadQrBtn').href = qrImageUrl;
    document.getElementById('downloadQrBtn').download = `qr_${barangayName.toLowerCase().replace(/\s+/g, '_')}.png`;
    
    document.getElementById('qrModalBody').innerHTML = `
        <div class="p-3">
            <img src="${qrImageUrl}" alt="QR Code" class="img-fluid mb-3" style="max-width: 300px;">
            <h5>${barangayName}</h5>
            <p class="text-muted">Resident Registration</p>
            <div class="bg-light p-2 rounded">
                <small class="text-muted" style="word-break: break-all;">${registrationUrl}</small>
            </div>
            <p class="mt-3 mb-0"><small class="text-muted">Scan this QR code to register as a resident</small></p>
        </div>
    `;
    
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    qrModal.show();
}

window.printQrCode = function() {
    if (!window.currentQrData.imageUrl) return;
    
    const printContent = `
        <div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">
            <h1 style="font-size: 36px; margin-bottom: 10px;">${window.currentQrData.name}</h1>
            <h2 style="font-size: 28px; color: #666; margin-bottom: 30px;">Resident Registration</h2>
            <div style="margin: 30px auto; width: 400px;">
                <img src="${window.currentQrData.imageUrl}" style="width: 400px; height: 400px;">
            </div>
            <h3 style="font-size: 24px; margin: 30px 0;">Scan to Register</h3>
            <p style="font-size: 16px; word-break: break-all; max-width: 500px; margin: 20px auto;">
                ${window.currentQrData.registrationUrl}
            </p>
            <p style="margin-top: 50px; font-size: 14px; color: #666;">
                For assistance, contact the Barangay Office
            </p>
        </div>
    `;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print QR Code - ${window.currentQrData.name}</title>
            <style>
                body { margin: 0; padding: 0; }
                @media print {
                    @page { margin: 0; }
                }
            </style>
        </head>
        <body>
            ${printContent}
            <script>
                window.onload = function() {
                    window.print();
                    window.onafterprint = function() {
                        window.close();
                    }
                }
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

window.showNotification = function(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>