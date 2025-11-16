@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Resident Profile</h2>
    <form action="{{ route('resident_profiles.update', $residentProfile->id) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('resident_profiles._form')
    </form>
</div>
@endsection
