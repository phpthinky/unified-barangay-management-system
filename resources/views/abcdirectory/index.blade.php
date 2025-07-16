@extends('layouts.app')
@section('title','ABC Directory')

@section('content')
<h3 class="mb-4">ABC Directory – Barangay Officials</h3>

<input type="text" class="form-control mb-3" placeholder="Search barangay...">

<table class="table table-striped">
  <thead>
    <tr>
      <th>Name</th><th>Position</th><th>Term</th><th>Contact</th><th>Action</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Pedro Reyes</td><td>Barangay Captain</td>
      <td>Jun 2022 – Jun 2025</td><td>0917‑123‑4567</td>
      <td><button class="btn btn-sm btn-outline-primary">View Profile</button></td>
    </tr>
    <tr>
      <td>Laura Ruiz</td><td>Barangay Secretary</td>
      <td>Jun 2022 – Jun 2025</td><td>0917‑234‑5678</td>
      <td><button class="btn btn-sm btn-outline-primary">View Profile</button></td>
    </tr>
  </tbody>
</table>
@endsection
