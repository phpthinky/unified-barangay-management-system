@extends('layouts.app')
@section('title','Archive Officials')

@section('content')
<h3 class="mb-4">Archive Old Officials</h3>

<p>Select outgoing barangay officials to archive their term:</p>

<table class="table">
  <thead>
    <tr>
      <th>Select</th><th>Name</th><th>Position</th><th>Term</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><input type="checkbox"></td>
      <td>Pedro Reyes</td>
      <td>Barangay Captain</td>
      <td>Jun 2019 – Jun 2022</td>
    </tr>
    <tr>
      <td><input type="checkbox"></td>
      <td>Laura Ruiz</td>
      <td>Secretary</td>
      <td>Jun 2019 – Jun 2022</td>
    </tr>
  </tbody>
</table>

<button class="btn btn-danger">Archive Selected Officials</button>
@endsection
