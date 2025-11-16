{{-- FILE: resources/views/public/barangay/register/password.blade.php - UPDATED --}}
@extends('layouts.public')

@section('title', 'Create Password - ' . $barangay->name)

@section('content')
<section class="bg-success text-white py-5">
    <div class="container">
        <h1 class="display-6 fw-bold">
            <i class="fas fa-check-circle me-3"></i>RBI Record Found!
        </h1>
        <p class="lead mb-0">Complete your account setup</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Verification Successful!</strong><br>
                            Your information matches our RBI registry:
                            <div class="mt-2">
                                <strong>Name:</strong> {{ session('rbi_check.first_name') }} {{ session('rbi_check.middle_name') }} {{ session('rbi_check.last_name') }} {{ session('rbi_check.suffix') }}<br>
                                <strong>Birth Date:</strong> {{ \Carbon\Carbon::parse(session('rbi_check.birth_date'))->format('F d, Y') }}
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Please correct the following:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('public.barangay.register.complete-rbi', $barangay->slug) }}">
                            @csrf

                            <h5 class="mb-3">Complete Your Account</h5>

                            <div class="mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required autofocus>
                                <small class="text-muted">Use a valid email - you'll use this to login</small>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required>
                                <small class="text-muted">Minimum 8 characters</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-check me-2"></i>Complete Registration
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection