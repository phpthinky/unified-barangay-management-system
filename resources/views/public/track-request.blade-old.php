{{-- resources/views/public/track-request.blade.php --}}
@extends('layouts.public')

@section('title', 'Track Request')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Track Your Request</h1>

    <form method="GET" action="{{ route('public.track-request') }}">
        <div class="mb-3">
            <label for="trackingNumber" class="form-label">Tracking Number</label>
            <input type="text" name="trackingNumber" id="trackingNumber" class="form-control" value="{{ old('trackingNumber') }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Track</button>
    </form>

    @isset($trackingNumber)
        <div class="mt-5">
            @if($request)
                <h4>Status: {{ ucfirst($request->status) }}</h4>
                <p>Type: {{ ucfirst($type) }}</p>
                <p>Tracking #: {{ $trackingNumber }}</p>
            @else
                <div class="alert alert-danger">
                    No request found for tracking number: {{ $trackingNumber }}
                </div>
            @endif
        </div>
    @endisset
</div>
@endsection
