{{-- FILE: resources/views/auth/reset-password.blade.php --}}
@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-gradient py-5 px-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <!-- Card -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <!-- Header -->
                    <div class="card-header text-white text-center py-5" style="background: linear-gradient(to right, #667eea, #764ba2);">
                        <div class="mx-auto bg-white rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-lock text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="fw-bold mb-2">Reset Password</h2>
                        <p class="mb-0 text-white-50">Enter your new password below</p>
                    </div>

                    <!-- Content -->
                    <div class="card-body p-4 p-md-5">
                        <!-- Error Messages -->
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="fas fa-times-circle me-2"></i>
                                <div>{{ session('error') }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Reset Password Form -->
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}">

                            <!-- Email (read-only) -->
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-1"></i> Email Address
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    class="form-control form-control-lg"
                                    value="{{ $email }}"
                                    readonly
                                    style="background-color: #f8f9fa;"
                                >
                            </div>

                            <!-- New Password -->
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-1"></i> New Password
                                </label>
                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="password" 
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        placeholder="Enter new password"
                                        required
                                        minlength="8"
                                    >
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-icon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Minimum 8 characters</small>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-1"></i> Confirm New Password
                                </label>
                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        name="password_confirmation" 
                                        id="password_confirmation" 
                                        class="form-control form-control-lg"
                                        placeholder="Confirm new password"
                                        required
                                        minlength="8"
                                    >
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Password Requirements -->
                            <div class="alert alert-light border mb-4">
                                <small class="text-muted d-block mb-2 fw-semibold">Password must contain:</small>
                                <ul class="list-unstyled small mb-0">
                                    <li><i class="fas fa-check text-success me-2"></i> At least 8 characters</li>
                                    <li><i class="fas fa-check text-success me-2"></i> Mix of letters and numbers (recommended)</li>
                                    <li><i class="fas fa-check text-success me-2"></i> Special characters (recommended)</li>
                                </ul>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                Reset Password
                            </button>

                            <!-- Back to Login -->
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Real-time password confirmation check
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirm = this.value;
    
    if (confirm && password !== confirm) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});
</script>
@endsection