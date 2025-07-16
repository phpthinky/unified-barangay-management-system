{{-- resources/views/permits/index.blade.php --}}
@extends('layouts.app')
@section('title','Business Permit Requests')

@section('content')
<h3 class="mb-4">Business Permit Requests</h3>

<table class="table table-hover align-middle">
  <thead class="table-light">
    <tr>
      <th>#</th><th>Business Name</th><th>Owner</th><th>Status</th><th>Attachments</th><th>Action</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>1</td>
      <td>Sablayan Sari‑Sari Store</td>
      <td>Maria Santos</td>
      <td><span class="badge bg-warning">Pending</span></td>
      <td>
        <a href="#" class="btn btn-sm btn-outline-secondary">View Files</a>
      </td>
      <td>
        <button class="btn btn-sm btn-success">Approve</button>
        <button class="btn btn-sm btn-danger">Reject</button>
      </td>
    </tr>
    <tr>
      <td>2</td>
      <td>Lanao Print Shop</td>
      <td>Carlos Reyes</td>
      <td><span class="badge bg-success">Approved</span></td>
      <td><a href="#" class="btn btn-sm btn-outline-secondary">View Files</a></td>
      <td><button class="btn btn-sm btn-secondary" disabled>Processed</button></td>
    </tr>
  </tbody>
</table>
@endsection
