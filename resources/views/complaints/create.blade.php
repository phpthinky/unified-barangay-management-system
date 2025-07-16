{{-- resources/views/complaints/create.blade.php --}}
@extends('layouts.app')
@section('title','File Complaint')
@section('content')
<h3 class="mb-4">File a Complaint</h3>

<form method="POST" action="{{ route('complaints.store') }}" class="col-md-6">
    @csrf
    <div class="mb-3">
        <label>Category</label>
        <select name="category" class="form-select">
            <option value="sanitation">Sanitation</option>
            <option value="noise">Noise Disturbance</option>
            <option value="peace">Peace & Order</option>
            <option value="others">Others</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Details</label>
        <textarea name="details" rows="4" class="form-control" required></textarea>
    </div>
    <button class="btn btn-primary">Submit Complaint</button>
</form>
@endsection
