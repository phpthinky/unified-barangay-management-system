{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')
@section('title','Notifications')
@section('content')
<h3 class="mb-4">Notifications</h3>

<ul class="list-group">
@forelse($notifications as $n)
    <li class="list-group-item d-flex justify-content-between align-items-start">
        <div>
            <strong>{{ $n->data['title'] ?? 'System' }}</strong><br>
            <span>{{ $n->data['message'] ?? '' }}</span>
        </div>
        <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
    </li>
@empty
    <li class="list-group-item text-center">No notifications yet.</li>
@endforelse
</ul>
@endsection
