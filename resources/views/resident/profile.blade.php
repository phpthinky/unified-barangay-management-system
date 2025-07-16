{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.app')
@section('title','My Profile')

@section('content')
<h3 class="mb-4">My Profile</h3>

<div class="card shadow-sm col-md-6 p-4">
  <form>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Full Name</label>
        <input class="form-control" value="Juan Dela Cruz" disabled>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input class="form-control" value="juan@email.com" disabled>
      </div>
      <div class="col-md-6">
        <label class="form-label">Mobile Number</label>
        <input class="form-control" value="0917‑123‑4567" disabled>
      </div>
      <div class="col-md-6">
        <label class="form-label">Barangay</label>
        <input class="form-control" value="Barangay Uno" disabled>
      </div>
    </div>

    <hr class="my-4">

    <h5>Change Password</h5>
    <div class="row g-3">
      <div class="col-md-6">
        <label>New Password</label>
        <input type="password" class="form-control">
      </div>
      <div class="col-md-6">
        <label>Confirm Password</label>
        <input type="password" class="form-control">
      </div>
    </div>
    <button class="btn btn-primary mt-3">Update Password</button>
  </form>
</div>
@endsection
