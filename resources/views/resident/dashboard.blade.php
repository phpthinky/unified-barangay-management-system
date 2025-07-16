@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<h3 class="mb-4">Welcome, {{ auth()->user()->name }}</h3>

<div class="row g-4">
    <div class="col-md-4">
        <a href="#" class="text-decoration-none">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Request Document</h5>
                    <p class="card-text text-muted">Clearance | Indigency | Permit</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="#" class="text-decoration-none">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">My Requests</h5>
                    <p class="card-text text-muted">Track status & QR codes</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="#" class="text-decoration-none">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">File Complaint</h5>
                    <p class="card-text text-muted">Noise, sanitation, etc.</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
