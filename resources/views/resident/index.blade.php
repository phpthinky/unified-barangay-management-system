{{-- resources/views/residents/index.blade.php --}}
@extends('layouts.app')
@section('title','Resident Records')
@section('content')
<h3 class="mb-3">Resident Records</h3>
<input type="text" class="form-control mb-3" placeholder="Search by name / address …">
<table class="table table-sm table-bordered">
  <thead class="table-light"><tr><th>Name</th><th>Age</th><th>Purok</th><th>Actions</th></tr></thead>
  <tbody>
    <tr><td>Maria Lopez</td><td>34</td><td>Purok 3</td>
        <td><a class="btn btn-sm btn-outline-primary">View</a></td></tr>
  </tbody>
</table>
@endsection
