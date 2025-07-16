{{-- resources/views/users/create.blade.php --}}
@extends('layouts.app')
@section('title','Add User')
@section('content')
<h3 class="mb-4">Add Staff / Official</h3>
<form class="col-md-6">
  <div class="mb-3"><label>Name</label><input class="form-control"></div>
  <div class="mb-3"><label>Email</label><input type="email" class="form-control"></div>
  <div class="mb-3"><label>Role</label>
    <select class="form-select">
      <option value="secretary">Secretary</option>
      <option value="lupon">Lupon</option>
      <option value="clerk">Clerk</option>
      <option value="captain">Barangay Captain</option>
    </select>
  </div>
  <button class="btn btn-primary">Create</button>
</form>
@endsection
