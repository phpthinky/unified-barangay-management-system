{{-- resources/views/requests/create.blade.php --}}
@extends('layouts.app')
@section('title','Request Document')
@section('content')
<h3 class="mb-4">Request a Barangay Document</h3>

<form action="{{ route('requests.store') }}" method="POST" class="col-md-6">
    @csrf
    <div class="mb-3">
        <label class="form-label">Document Type</label>
        <select name="type" class="form-select" required>
            <option value="clearance">Barangay Clearance</option>
            <option value="indigency">Certificate of Indigency</option>
            <option value="permit">Business Permit</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Purpose</label>
        <input type="text" name="purpose" class="form-control" required>
    </div>
    <button class="btn btn-primary">Submit Request</button>
</form>
@endsection
