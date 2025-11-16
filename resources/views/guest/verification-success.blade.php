{{-- FILE: resources/views/guest/verification-success.blade.php --}}
@extends('layouts.guest')

@section('title', 'Email Verified Successfully')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-gradient py-5 px-3" style="background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%, #8b5cf6 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <!-- Success Card -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <!-- Animated Success Icon -->
                    <div class="card-header text-white text-center py-5 position-relative" style="background: linear-gradient(to right, #10b981, #059669);">
                        <!-- Confetti Animation -->
                        <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="pointer-events: none;">
                            <div class="confetti"></div>
                        </div>
                        
                        <div class="position-relative">
                            <div class="mx-auto bg-white rounded-circle d-flex align-items-center justify-content-center mb-3 animate-bounce" style="width: 96px; height: 96px;">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h2 class="fw-bold mb-2">🎉 Email Verified!</h2>
                            <p class="mb-0 text-white-50">Your account is now fully activated</p>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="card-body p-4 p-md-5">
                        <!-- Post-verification Message (from registration) -->
                        @if (session('post_verification_message'))
                            @php
                                $message = session('post_verification_message');
                                $alertClass = $message['type'] === 'success' ? 'alert-success' : 'alert-warning';
                                $iconClass = $message['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
                            @endphp
                            <div class="alert {{ $alertClass }} d-flex" role="alert">
                                <i class="fas {{ $iconClass }} me-2 mt-1 flex-shrink-0"></i>
                                <div class="small">{{ $message['message'] }}</div>
                            </div>
                        @endif

                        <!-- Welcome Message -->
                        <div class="text-center mb-4">
                            <h4 class="fw-semibold mb-2">
                                Welcome, {{ $user->first_name }}!
                            </h4>
                            <p class="text-muted">
                                Your email <span class="fw-semibold text-primary">{{ $user->email }}</span> has been successfully verified.
                            </p>
                        </div>

                        <!-- Services Available -->
                        <div class="bg-light rounded-3 p-4 mb-4">
                            <h6 class="fw-semibold mb-3 d-flex align-items-center">
                                <i class="fas fa-star text-warning me-2"></i>
                                You can now access:
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-start mb-3">
                                    <i class="fas fa-check-circle text-success me-2 mt-1 flex-shrink-0"></i>
                                    <div class="small">
                                        <strong>Document Requests</strong> - Barangay Clearance, Certificates, and more
                                    </div>
                                </li>
                                <li class="d-flex align-items-start mb-3">
                                    <i class="fas fa-check-circle text-success me-2 mt-1 flex-shrink-0"></i>
                                    <div class="small">
                                        <strong>Business Permits</strong> - Apply and track permit applications
                                    </div>
                                </li>
                                <li class="d-flex align-items-start mb-3">
                                    <i class="fas fa-check-circle text-success me-2 mt-1 flex-shrink-0"></i>
                                    <div class="small">
                                        <strong>Complaint Management</strong> - File and track complaint cases
                                    </div>
                                </li>
                                <li class="d-flex align-items-start mb-0">
                                    <i class="fas fa-check-circle text-success me-2 mt-1 flex-shrink-0"></i>
                                    <div class="small">
                                        <strong>Profile Management</strong> - Update your information anytime
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- CTA Button -->
                        <a href="{{ route('resident.dashboard') }}" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center">
                            <i class="fas fa-home me-2"></i>
                            Go to Dashboard
                        </a>

                        <!-- Additional Info -->
                        <div class="mt-4 pt-4 border-top text-center">
                            <p class="small text-muted mb-0">
                                Need help? Contact 
                                <a href="mailto:{{ $user->barangay->email ?? 'support@barangay.local' }}" class="text-decoration-none fw-semibold">
                                    {{ $user->barangay->name ?? 'Barangay Office' }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes confetti-fall {
        from {
            transform: translateY(-100%) rotate(0deg);
            opacity: 1;
        }
        to {
            transform: translateY(100vh) rotate(360deg);
            opacity: 0;
        }
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    .animate-bounce {
        animation: bounce 1s ease-in-out infinite;
    }

    .confetti::before,
    .confetti::after {
        content: '';
        position: absolute;
        width: 10px;
        height: 10px;
        background: white;
        animation: confetti-fall 3s ease-in-out infinite;
    }

    .confetti::before {
        left: 20%;
        animation-delay: 0.2s;
    }

    .confetti::after {
        right: 20%;
        animation-delay: 0.5s;
        background: rgba(255, 255, 255, 0.7);
    }
</style>

<script>
    // Auto-redirect after 5 seconds
    let countdown = 5;
    const timer = setInterval(() => {
        countdown--;
        if (countdown <= 0) {
            clearInterval(timer);
            window.location.href = "{{ route('resident.dashboard') }}";
        }
    }, 1000);
</script>
@endsection