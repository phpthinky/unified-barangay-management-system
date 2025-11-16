{{-- resources/views/public/barangay/contact.blade.php --}}
@extends('layouts.public')

@section('title', $barangay->name . ' Contact')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Contact Barangay {{ $barangay->name }}</h1>

    <p><strong>Address:</strong> {{ $barangay->address ?? 'N/A' }}</p>
    <p><strong>Phone:</strong> {{ $barangay->phone ?? 'N/A' }}</p>
    <p><strong>Email:</strong> {{ $barangay->email ?? 'N/A' }}</p>
</div>
@endsection
