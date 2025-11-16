{{-- FILE: resources/views/public/barangay/register/rbi-check.blade.php - UPDATED --}}
@extends('layouts.public')

@section('title', 'RBI Verification - ' . $barangay->name)

@section('content')
<section class="bg-primary text-white py-5">
    <div class="container">
        <h1 class="display-6 fw-bold">
            <i class="fas fa-search me-3"></i>RBI Verification
        </h1>
        <p class="lead mb-0">Enter your details to verify your RBI record</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <a href="{{ route('public.barangay.register', $barangay->slug) }}" class="btn btn-sm btn-secondary mb-4">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Please enter your name and birth date <strong>exactly as registered</strong> in the RBI.
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

                        <form method="POST" action="{{ route('public.barangay.register.check-rbi', $barangay->slug) }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           name="first_name" value="{{ old('first_name') }}" required autofocus>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                           name="middle_name" value="{{ old('middle_name') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Extension (Jr., Sr., III, etc.)</label>
                                    <input type="text" class="form-control" name="suffix" 
                                           value="{{ old('suffix') }}" placeholder="Optional">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Birth Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                           name="birth_date" value="{{ old('birth_date') }}" required>
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-search me-2"></i>Search RBI Registry
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection