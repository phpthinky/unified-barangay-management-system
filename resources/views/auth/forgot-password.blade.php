{{-- FILE: resources/views/auth/forgot-password.blade.php --}}
@extends('layouts.guest')

@section('title', 'Forgot Password')

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
                            <i class="fas fa-key text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="fw-bold mb-2">Forgot Password?</h2>
                        <p class="mb-0 text-white-50">No worries, we'll send you reset instructions</p>
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

                        <!-- Info Message -->
                        <div class="alert alert-info d-flex align-items-start mb-4" role="alert">
                            <i class="fas fa-info-circle me-2 mt-1 flex-shrink-0"></i>
                            <div class="small">
                                Enter your email address and we'll send you a link to reset your password.
                            </div>
                        </div>

                        <!-- Forgot Password Form -->
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-1"></i> Email Address
                                </label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="email" 
                                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                                    placeholder="Enter your email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-paper-plane me-2"></i>
                                Send Reset Link
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

                <!-- Help Box -->
                <div class="card mt-4 bg-white bg-opacity-75 border-0">
                    <div class="card-body">
                        <h6 class="fw-bold d-flex align-items-center mb-3">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            Need Help?
                        </h6>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Check your spam folder</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Make sure you entered the correct email</li>
                            <li class="mb-0"><i class="fas fa-check text-success me-2"></i> Contact barangay office if issues persist</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection