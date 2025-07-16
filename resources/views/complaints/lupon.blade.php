@extends('layouts.app')
@section('title','Lupon – Handle Complaints')

@section('content')
<h3 class="mb-4">Lupon – Complaint Management</h3>

<table class="table">
  <thead>
    <tr>
      <th>#</th><th>Resident</th><th>Category</th>
      <th>Complaint</th><th>Status</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>1</td>
      <td>Juan Dela Cruz</td>
      <td>Sanitation</td>
      <td>Overflowing trash near main gate</td>
      <td><span class="badge bg-warning">Pending</span></td>
      <td>
        <button class="btn btn-sm btn-success">Mark Resolved</button>
        <button class="btn btn-sm btn-primary">Add Notes</button>
      </td>
    </tr>
    <tr>
      <td>2</td>
      <td>Maria Santos</td>
      <td>Noise</td>
      <td>Loud music after midnight</td>
      <td><span class="badge bg-success">Resolved</span></td>
      <td>
        <button class="btn btn-sm btn-secondary" disabled>Resolved</button>
      </td>
    </tr>
  </tbody>
</table>
@endsection
