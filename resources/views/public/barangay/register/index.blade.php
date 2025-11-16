{{-- FILE: resources/views/public/barangay/register/index.blade.php --}}
@extends('layouts.public')

@section('title', 'Register - ' . $barangay->name)

@section('content')
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold mb-3">
                    <i class="fas fa-user-plus me-3"></i>Resident Registration
                </h1>
                <p class="lead mb-0">Register as a resident of <strong>{{ $barangay->name }}</strong></p>
            </div>
            <div class="col-lg-4 text-end">
                @if($barangay->logo_url)
                    <img src="{{ $barangay->logo_url }}" alt="{{ $barangay->name }}" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid white;">
                @endif
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Registry of Barangay Inhabitants (RBI)</strong> is our official resident database. 
                            Residents registered in RBI can request barangay documents after 6 months of residency.
                        </div>
                        
                        <h4 class="mb-4">Are you registered in {{ $barangay->name }}'s RBI?</h4>
                        
                        <form method="POST" action="{{ route('public.barangay.register.continue', $barangay->slug) }}">
                            @csrf
                            
                            <div class="form-check mb-3 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="has_rbi" id="rbi_yes" value="yes" required>
                                <label class="form-check-label fw-bold" for="rbi_yes">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    YES - I am registered in the RBI
                                </label>
                                <small class="d-block text-muted ms-4 mt-1">
                                    We will search for your record and link it automatically
                                </small>
                            </div>
                            
                            <div class="form-check mb-4 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="has_rbi" id="rbi_no" value="no" required>
                                <label class="form-check-label fw-bold" for="rbi_no">
                                    <i class="fas fa-times-circle text-warning me-2"></i>
                                    NO - I am not registered yet
                                </label>
                                <small class="d-block text-muted ms-4 mt-1">
                                    You'll need to visit the barangay office for RBI registration before requesting documents
                                </small>
                            </div>

                            @error('has_rbi')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                Continue <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection