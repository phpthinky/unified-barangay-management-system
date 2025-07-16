{{-- resources/views/requests/index.blade.php --}}
@extends('layouts.app')
@section('title','My Requests')
@section('content')
<h3 class="mb-4">My Requests</h3>
 <a href="{{ route('requests.create') }}" class="btn btn-outline-primary mb-3">
        + New Resquest
    </a>
<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>#</th><th>Type</th><th>Purpose</th><th>Status</th><th>QR</th>
        </tr>
    </thead>
    <tbody>
        @foreach($requests as $req)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ ucfirst($req->type) }}</td>
            <td>{{ $req->purpose }}</td>
            <td>
                <span class="badge bg-{{ $req->status=='approved'?'success':'secondary' }}">
                    {{ ucfirst($req->status) }}
                </span>
            </td>
            <td>
                @if($req->qr_code)
                    <img src="{{ asset('storage/qrcodes/'.$req->qr_code) }}" width="60">
                @else
                    —
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
