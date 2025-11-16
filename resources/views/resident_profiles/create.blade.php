@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Resident Profile</h2>
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    <form action="{{ route('resident_profiles.store') }}" method="POST" enctype="multipart/form-data">
        @include('resident_profiles._form')
    </form>
</div>
@endsection
