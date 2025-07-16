{{-- resources/views/complaints/index.blade.php --}}
@extends('layouts.app')
@section('title','My Complaints')
@section('content')
<h3 class="mb-4">My Complaints</h3>

 <a href="{{ route('complaints.create') }}" class="btn btn-outline-primary mb-3">
        + File complaint
    </a>
<table class="table table-striped">
<thead><tr><th>#</th><th>Category</th><th>Status</th><th>Filed</th></tr></thead>
<tbody>
@foreach($complaints as $c)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ ucfirst($c->category) }}</td>
    <td><span class="badge bg-{{ $c->status=='resolved'?'success':'warning' }}">{{ ucfirst($c->status) }}</span></td>
    <td>{{ $c->created_at->format('M d, Y') }}</td>
</tr>
@endforeach
</tbody>
</table>
@endsection
