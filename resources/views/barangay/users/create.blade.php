@extends('layouts.barangay')

@section('title', 'Add New Staff')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('barangay.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('barangay.users.index') }}">Staff Management</a></li>
                <li class="breadcrumb-item active">Add New Staff</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">Add New Staff Member</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('barangay.users.store') }}">
                        @csrf

                        <!-- Personal Information -->
                        <h5 class="mb-3">Personal Information</h5>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                       id="middle_name" name="middle_name" value="{{ old('middle_name') }}">
                                @error('middle_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label for="suffix" class="form-label">Suffix</label>
                                <input type="text" class="form-control @error('suffix') is-invalid @enderror" 
                                       id="suffix" name="suffix" value="{{ old('suffix') }}" placeholder="Jr., Sr., III">
                                @error('suffix')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-5">
                                <label for="birth_date" class="form-label">Birth Date</label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                       id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-5">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select @error('gender') is-invalid @enderror" 
                                        id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <h5 class="mb-3 mt-4">Contact Information</h5>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                       id="phone_number" name="phone_number" value="{{ old('phone_number') }}" 
                                       placeholder="09XXXXXXXXX">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="2">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Role & Position -->
                        <h5 class="mb-3 mt-4">Role & Position</h5>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('-', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="position_title" class="form-label">Position Title</label>
                                <input type="text" class="form-control @error('position_title') is-invalid @enderror" 
                                       id="position_title" name="position_title" value="{{ old('position_title') }}" 
                                       placeholder="e.g., Head of Records">
                                @error('position_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Councilor-specific fields -->
                            <div id="councilor-fields" style="display: none;" class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="councilor_number" class="form-label">Councilor Number <span class="text-danger">*</span></label>
                                        <select class="form-select @error('councilor_number') is-invalid @enderror" 
                                                id="councilor_number" name="councilor_number">
                                            <option value="">Select Number</option>
                                            @for($i = 1; $i <= 7; $i++)
                                                <option value="{{ $i }}" {{ old('councilor_number') == $i ? 'selected' : '' }}>
                                                    Councilor {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('councilor_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="committee" class="form-label">Committee Assignment <span class="text-danger">*</span></label>
                                        <select class="form-select @error('committee') is-invalid @enderror" 
                                                id="committee" name="committee">
                                            <option value="">Select Committee</option>
                                            @foreach($committees as $key => $label)
                                                <option value="{{ $key }}" {{ old('committee') == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('committee')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Term fields -->
                            <div id="term-fields" style="display: none;" class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="term_start" class="form-label">Term Start</label>
                                        <input type="date" class="form-control @error('term_start') is-invalid @enderror" 
                                               id="term_start" name="term_start" value="{{ old('term_start', now()->format('Y-m-d')) }}">
                                        @error('term_start')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Default: Today's date</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="term_end" class="form-label">Term End (Expiration Date)</label>
                                        <input type="date" class="form-control @error('term_end') is-invalid @enderror" 
                                               id="term_end" name="term_end" value="{{ old('term_end', now()->addYears(3)->format('Y-m-d')) }}">
                                        @error('term_end')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Default: 3 years from start date</small>
                                    </div>

                                    <div class="col-12">
                                        <div class="alert alert-warning small mb-0">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            <strong>Auto-Archive Notice:</strong> This account will be automatically archived after the term end date. 
                                            The user will be notified 30 days before expiration.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Security -->
                        <h5 class="mb-3 mt-4">Account Security</h5>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                <small class="form-text text-muted">Minimum 8 characters</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Create User
                            </button>
                            <a href="{{ route('barangay.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle text-primary me-1"></i> Information
                    </h6>
                    <hr>
                    <p class="small text-muted mb-2">
                        <strong>Role Permissions:</strong>
                    </p>
                    <ul class="small text-muted">
                        <li><strong>Secretary/Treasurer:</strong> Process documents, manage records</li>
                        <li><strong>Staff:</strong> Assist with daily operations</li>
                        <li><strong>Councilor:</strong> Handle complaints, committee duties</li>
                        <li><strong>Lupon Member:</strong> Mediate barangay disputes</li>
                    </ul>
                    
                    <div class="alert alert-info small mt-3 mb-0">
                        <i class="bi bi-shield-check me-1"></i> All accounts are auto-verified and will be active immediately.
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-calendar-x text-warning me-1"></i> Term Expiration
                    </h6>
                    <hr>
                    <p class="small text-muted">
                        <strong>Elected positions</strong> (Councilor, Secretary) have 3-year terms that start from the date of assumption.
                    </p>
                    
                    <div class="alert alert-warning small mb-0">
                        <strong>Auto-Archive System:</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>30 days before term end: Email reminder sent</li>
                            <li>7 days before: Final reminder sent</li>
                            <li>On term end date: Account auto-archived</li>
                        </ul>
                    </div>
                    
                    <p class="small text-muted mt-2 mb-0">
                        <i class="bi bi-lightbulb me-1"></i> Archived users can be restored if re-elected or extended.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const councilorFields = document.getElementById('councilor-fields');
    const termFields = document.getElementById('term-fields');
    const termStartInput = document.getElementById('term_start');
    const termEndInput = document.getElementById('term_end');
    
    const termRoles = ['barangay-secretary'];

    function toggleFields() {
        const selectedRole = roleSelect.value;

        // Councilor fields removed - officials are now managed via organizational chart
        councilorFields.style.display = 'none';
        document.getElementById('councilor_number').required = false;
        document.getElementById('committee').required = false;
        
        // Toggle term fields
        if (termRoles.includes(selectedRole)) {
            termFields.style.display = 'block';
        } else {
            termFields.style.display = 'none';
        }
    }
    
    // Auto-calculate term end date (3 years from start)
    termStartInput.addEventListener('change', function() {
        if (this.value) {
            const startDate = new Date(this.value);
            startDate.setFullYear(startDate.getFullYear() + 3);
            
            // Format to YYYY-MM-DD
            const endDate = startDate.toISOString().split('T')[0];
            termEndInput.value = endDate;
        }
    });
    
    roleSelect.addEventListener('change', toggleFields);
    toggleFields(); // Initialize on page load
});
</script>
@endsection