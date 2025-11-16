{{-- FILE: resources/views/guest/verify-email.blade.php --}}
@extends('layouts.guest')

@section('title', 'Verify Your Email')

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
                            <i class="fas fa-envelope text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="fw-bold mb-2">Verify Your Email</h2>
                        <p class="mb-0 text-white-50">Almost there! Just one more step...</p>
                    </div>

                    <!-- Content -->
                    <div class="card-body p-4 p-md-5">
                        <!-- Success/Error Messages -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <div>{{ session('success') }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="fas fa-times-circle me-2"></i>
                                <div>{{ session('error') }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <div>{{ session('info') }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Email Info -->
                        <div class="text-center mb-4">
                            <p class="text-muted mb-2">We've sent a 6-digit verification code to:</p>
                            <p class="fw-bold text-primary fs-5">{{ $user->email }}</p>
                        </div>

                        <!-- Verification Form -->
                        <form method="POST" action="{{ route('verification.verify') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="verification_code" class="form-label text-center d-block fw-semibold">
                                    Enter Verification Code
                                </label>
                                <input 
                                    type="text" 
                                    name="verification_code" 
                                    id="verification_code" 
                                    maxlength="6"
                                    pattern="[0-9]{6}"
                                    placeholder="000000"
                                    required
                                    autofocus
                                    class="form-control form-control-lg text-center fw-bold fs-2 @error('verification_code') is-invalid @enderror"
                                    style="letter-spacing: 0.5rem;"
                                    value="{{ old('verification_code') }}"
                                >
                                @error('verification_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Verify Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-check-circle me-2"></i>
                                Verify Email
                            </button>
                        </form>

                        <!-- Resend Code -->
                        <div class="mt-4 pt-4 border-top">
                            <p class="text-center text-muted mb-3 small">
                                Didn't receive the code?
                            </p>
                            
                            @if ($user->canResendVerificationCode())
                                <form method="POST" action="{{ route('verification.resend') }}" class="text-center">
                                    @csrf
                                    <button type="submit" class="btn btn-link text-decoration-none">
                                        <i class="fas fa-redo me-1"></i> Resend Verification Code
                                    </button>
                                </form>
                            @else
                                <p class="text-center small text-muted">
                                    You can request a new code in 
                                    <span class="fw-bold text-primary" id="countdown">{{ $user->secondsUntilCanResend() }}</span> 
                                    seconds
                                </p>
                            @endif
                        </div>

                        <!-- Help -->
                        <div class="mt-4 text-center">
                            <p class="small text-muted mb-0">
                                Need help? Contact 
                                <a href="mailto:{{ $user->barangay->email ?? 'support@barangay.local' }}" class="text-decoration-none">
                                    {{ $user->barangay->name ?? 'Barangay Office' }}
                                </a>
                            </p>
                        </div>

                        <!-- Logout -->
                        <div class="mt-3 text-center">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-link btn-sm text-muted text-decoration-none">
                                    <i class="fas fa-sign-out-alt me-1"></i> Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="card mt-4 bg-white bg-opacity-75 border-0">
                    <div class="card-body">
                        <h6 class="fw-bold d-flex align-items-center mb-3">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Why verify?
                        </h6>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Access all barangay services</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Request official documents</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Apply for business permits</li>
                            <li class="mb-0"><i class="fas fa-check text-success me-2"></i> File and track complaints</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if (!$user->canResendVerificationCode())
<script>
    let seconds = {{ $user->secondsUntilCanResend() }};
    const countdownElement = document.getElementById('countdown');
    
    const interval = setInterval(() => {
        seconds--;
        if (countdownElement) {
            countdownElement.textContent = seconds;
        }
        
        if (seconds <= 0) {
            clearInterval(interval);
            location.reload();
        }
    }, 1000);
</script>
@endif
@endsection