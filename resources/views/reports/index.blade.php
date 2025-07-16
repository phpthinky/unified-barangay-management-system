@extends('layouts.app')
@section('title','Reports & Stats')

@section('content')
<h3 class="mb-4">Reports & Monitoring</h3>

<div class="row mb-4">
  <div class="col-md-4">
    <div class="card p-3">
      <h5>Total Requests</h5>
      <h2>120</h2>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      <h5>Pending Complaints</h5>
      <h2>8</h2>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      <h5>Active Officials</h5>
      <h2>7</h2>
    </div>
  </div>
</div>

<div class="graph-placeholder p-5 bg-light text-center mb-4">
  [Line/Bar Chart showing monthly requests]
</div>

<button class="btn btn-outline-primary">Download Report (PDF)</button>
@endsection
