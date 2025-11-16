{{-- FILE: resources/views/public/barangay/register/not-found.blade.php --}}
@extends('layouts.public')

@section('title', 'RBI Not Found - ' . $barangay->name)

@section('content')
<section class="bg-warning text-dark py-5">
    <div class="container">
        <h1 class="display-6 fw-bold">
            <i class="fas fa-exclamation-triangle me-3"></i>RBI Record Not Found
        </h1>
        <p class="lead mb-0">We couldn't find your record in our registry</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>What This Means</h5>
                            <p>We searched for:</p>
                            <ul>
                                <li><strong>Name:</strong> {{ session('rbi_search.first_name') }} {{ session('rbi_search.middle_name') }} {{ session('rbi_search.last_name') }}</li>
                                <li><strong>Birth Date:</strong> {{ \Carbon\Carbon::parse(session('rbi_search.birth_date'))->format('F d, Y') }}</li>
                            </ul>
                            <p class="mb-0">No matching record was found in {{ $barangay->name }}'s RBI database.</p>
                        </div>

                        <h5 class="mb-3">You Have Two Options:</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary"><i class="fas fa-building me-2"></i>Option 1: Visit Barangay Office</h6>
                                        <p class="small">Register in the official RBI first, then create your account online for full document access.</p>
                                        <a href="{{ route('public.barangay.home', $barangay->slug) }}" class="btn btn-outline-primary btn-sm">
                                            View Office Hours
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border-warning h-100">
                                    <div class="card-body">
                                        <h6 class="text-warning"><i class="fas fa-user-plus me-2"></i>Option 2: Register Anyway</h6>
                                        <p class="small">Create an account now with <strong>limited access</strong>. You'll need RBI registration for document requests.</p>
                                        <a href="{{ route('public.barangay.register.full-form', $barangay->slug) }}" class="btn btn-warning btn-sm">
                                            Continue Registration
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="text-muted mb-3">Made a mistake?</p>
                            <a href="{{ route('public.barangay.register.rbi-check', $barangay->slug) }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-2"></i>Try Again with Different Information
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection