{{-- resources/views/public/barangay/services.blade.php --}}
@extends('layouts.public')

@section('title', $barangay->name . ' Services')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Services in {{ $barangay->name }}</h1>
    <p>Here are the services offered by Barangay {{ $barangay->name }}.</p>

    <ul class="list-group">
        @if($barangay->services && count($barangay->services))
            @foreach($barangay->services as $service)
                <li class="list-group-item">
                    <h5>{{ $service->name }}</h5>
                    <p>{{ $service->description }}</p>
                </li>
            @endforeach
        @else
            <li class="list-group-item">No services available at this time.</li>
        @endif
    </ul>
</div>
@endsection
