{{-- FILE: resources/views/public/welcome.blade.php --}}
@extends('layouts.public')

@section('title', 'Welcome to ' . ($settings->municipality_name ?? config('app.name')))

@section('content')
<div class="container my-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold">Warning!</h1>
        <p class="text-muted">You are in invalid welcome route</p>
    </div>
</div>
@endsection
