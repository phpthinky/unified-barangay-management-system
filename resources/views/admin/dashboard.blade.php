{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')
@section('title','Barangay Captain Dashboard')

@section('content')
<h3 class="mb-4">Barangay Captain Dashboard</h3>

{{-- ====== Top metric cards ====== --}}
<div class="row g-4 mb-4">
  <div class="col-xl-3 col-md-6">
    <div class="card text-white bg-primary shadow-sm">
      <div class="card-body text-center">
        <h6>Total Residents</h6>
        <h2>3,214</h2>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card text-white bg-success shadow-sm">
      <div class="card-body text-center">
        <h6>Approved Documents</h6>
        <h2>1,045</h2>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card text-white bg-warning shadow-sm">
      <div class="card-body text-center">
        <h6>Pending Requests</h6>
        <h2>54</h2>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card text-white bg-danger shadow-sm">
      <div class="card-body text-center">
        <h6>Open Complaints</h6>
        <h2>8</h2>
      </div>
    </div>
  </div>
</div>

{{-- ====== Quick links ====== --}}
<div class="row g-4 mb-4">
  <div class="col-md-4">
    <a href="{{ route('requests.index') }}" class="text-decoration-none">
      <div class="card h-100 shadow-sm">
        <div class="card-body text-center">
          <h4><i class="bi bi-file-earmark-text"></i></h4>
          <h5 class="mt-2">Manage Requests</h5>
          <p class="text-muted small mb-0">Approve or reject document applications.</p>
        </div>
      </div>
    </a>
  </div>
  <div class="col-md-4">
    <a href="{{ route('complaints.index') }}" class="text-decoration-none">
      <div class="card h-100 shadow-sm">
        <div class="card-body text-center">
          <h4><i class="bi bi-exclamation-circle"></i></h4>
          <h5 class="mt-2">Handle Complaints</h5>
          <p class="text-muted small mb-0">Assign or resolve community issues.</p>
        </div>
      </div>
    </a>
  </div>
  <div class="col-md-4">
    <a href="{{ route('reports.index') }}" class="text-decoration-none">
      <div class="card h-100 shadow-sm">
        <div class="card-body text-center">
          <h4><i class="bi bi-bar-chart-line"></i></h4>
          <h5 class="mt-2">View Reports</h5>
          <p class="text-muted small mb-0">Generate monthly statistics and PDF exports.</p>
        </div>
      </div>
    </a>
  </div>
</div>

{{-- ====== Two sample tables ====== --}}
<div class="row g-4">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-light fw-bold">Latest Pending Requests</div>
      <table class="table mb-0">
        <thead><tr><th>#</th><th>Resident</th><th>Type</th><th>Date</th></tr></thead>
        <tbody>
          <tr><td>1</td><td>Maria Lopez</td><td>Clearance</td><td>Jul 16</td></tr>
          <tr><td>2</td><td>Carlos Reyes</td><td>Business Permit</td><td>Jul 16</td></tr>
          <tr><td>3</td><td>Ana Cruz</td><td>Indigency</td><td>Jul 15</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-light fw-bold">Recent Complaints</div>
      <table class="table mb-0">
        <thead><tr><th>#</th><th>Resident</th><th>Issue</th><th>Status</th></tr></thead>
        <tbody>
          <tr><td>1</td><td>Juan Dela Cruz</td><td>Noise</td><td><span class="badge bg-warning">Pending</span></td></tr>
          <tr><td>2</td><td>Rosa Flores</td><td>Sanitation</td><td><span class="badge bg-success">Resolved</span></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
