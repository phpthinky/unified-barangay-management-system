{{-- FILE: resources/views/public/welcome.blade.php --}}
@extends('layouts.public')

@section('title', 'Welcome to ' . ($settings->municipality_name ?? config('app.name')))

@section('content')
<div class="container my-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold">Welcome to {{ $settings->municipality_name ?? 'Our Municipality' }}</h1>
        <p class="text-muted">Select a barangay below to view its public page</p>
    </div>

    <div class="row">
        @foreach($barangays as $barangay)
        <div class="col-md-4 mb-4">
            <a href="{{ $barangay->public_url }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm text-center">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        @if($barangay->logo)
                            <img src="{{ asset('storage/'.$barangay->logo) }}" 
                                 alt="{{ $barangay->name }}" class="img-fluid mb-3" style="max-height:100px;">
                        @elseif($barangay->qr_code_path)
                            <img src="{{ asset('storage/'.$barangay->qr_code_path) }}" 
                                 alt="{{ $barangay->name }}" class="img-fluid mb-3" style="max-height:100px;">
                        @else
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        @endif
                        <h5 class="card-title">{{ $barangay->name }}</h5>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
