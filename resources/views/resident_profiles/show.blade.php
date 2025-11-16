@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">My Profile</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Personal Information</h5>
            <div class="row">
                <div class="col-md-4"><strong>First Name:</strong> {{ $profile->first_name }}</div>
                <div class="col-md-4"><strong>Middle Name:</strong> {{ $profile->middle_name ?? 'N/A' }}</div>
                <div class="col-md-4"><strong>Last Name:</strong> {{ $profile->last_name }}</div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4"><strong>Mother's Maiden Name:</strong> {{ $profile->mother_maiden_name ?? 'N/A' }}</div>
                <div class="col-md-4"><strong>Birthdate:</strong> {{ $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->format('M d, Y') : 'N/A' }}</div>
                <div class="col-md-4"><strong>Gender:</strong> {{ $profile->gender ?? 'N/A' }}</div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4"><strong>Contact Number:</strong> {{ $profile->contact_number ?? 'N/A' }}</div>
                <div class="col-md-4"><strong>Email:</strong> {{ $profile->email ?? 'N/A' }}</div>
            </div>

            <hr>
            <h5 class="mb-3">Address</h5>
            <div class="row">
                <div class="col-md-3"><strong>House No.:</strong> {{ $profile->house_number ?? 'N/A' }}</div>
                <div class="col-md-3"><strong>Street:</strong> {{ $profile->street ?? 'N/A' }}</div>
                <div class="col-md-3"><strong>Purok:</strong> {{ $profile->purok ?? 'N/A' }}</div>
                <div class="col-md-3"><strong>Barangay:</strong> {{ $profile->barangay }}</div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4"><strong>Municipality:</strong> {{ $profile->municipality ?? 'Sablayan' }}</div>
                <div class="col-md-4"><strong>Province:</strong> {{ $profile->province ?? 'Occidental Mindoro' }}</div>
                <div class="col-md-4"><strong>Zipcode:</strong> {{ $profile->zipcode ?? 'N/A' }}</div>
            </div>

            <hr>
            <h5 class="mb-3">Identification</h5>
            <div class="row">
                <div class="col-md-4"><strong>Valid ID Type:</strong> {{ $profile->valid_id_type ?? 'N/A' }}</div>
                <div class="col-md-4"><strong>Valid ID Number:</strong> {{ $profile->valid_id_number ?? 'N/A' }}</div>
                <div class="col-md-4">
                    <strong>Valid ID File:</strong>
                    @if($profile->valid_id_path)
                        <a href="{{ asset('storage/'.$profile->valid_id_path) }}" target="_blank">View</a>
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <strong>Proof of Residency:</strong>
                    @if($profile->proof_of_residency_path)
                        <a href="{{ asset('storage/'.$profile->proof_of_residency_path) }}" target="_blank">View</a>
                    @else
                        N/A
                    @endif
                </div>
                <div class="col-md-4"><strong>Occupation:</strong> {{ $profile->occupation ?? 'N/A' }}</div>
                <div class="col-md-4"><strong>Civil Status:</strong> {{ $profile->civil_status ?? 'N/A' }}</div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4"><strong>Nationality:</strong> {{ $profile->nationality ?? 'Filipino' }}</div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('resident_profiles.edit') }}" class="btn btn-warning">Edit Profile</a>
    </div>
</div>
@endsection
