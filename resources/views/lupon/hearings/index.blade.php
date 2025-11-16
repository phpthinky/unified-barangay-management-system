{{-- resources/views/lupon/hearings/index.blade.php --}}
@extends('layouts.lupon')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-gavel"></i> Hearings</h2>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <small>Total Hearings</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['scheduled'] }}</h3>
                    <small>Scheduled</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['today'] }}</h3>
                    <small>Today</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['completed'] }}</h3>
                    <small>Completed</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Filters --}}
    <div class="btn-group mb-3" role="group">
        <a href="{{ route('lupon.hearings.index') }}" 
           class="btn btn-outline-primary {{ !request()->has('status') && !request()->has('upcoming') ? 'active' : '' }}">
            All Hearings
        </a>
        <a href="{{ route('lupon.hearings.index', ['status' => 'scheduled']) }}" 
           class="btn btn-outline-primary {{ request('status') == 'scheduled' ? 'active' : '' }}">
            Scheduled
        </a>
        <a href="{{ route('lupon.hearings.index', ['upcoming' => true]) }}" 
           class="btn btn-outline-warning {{ request('upcoming') ? 'active' : '' }}">
            <i class="fas fa-clock"></i> Upcoming (7 days)
        </a>
        <a href="{{ route('lupon.hearings.index', ['status' => 'completed']) }}" 
           class="btn btn-outline-success {{ request('status') == 'completed' ? 'active' : '' }}">
            Completed
        </a>
        <a href="{{ route('lupon.hearings.index', ['pending_docs' => true]) }}" 
           class="btn btn-outline-danger {{ request('pending_docs') ? 'active' : '' }}">
            <i class="fas fa-exclamation-triangle"></i> Pending Docs ({{ $stats['pending_docs'] }})
        </a>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('lupon.hearings.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by hearing # or complaint #..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_from" class="form-control" 
                               placeholder="From Date" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_to" class="form-control" 
                               placeholder="To Date" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Hearings Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hearing #</th>
                            <th>Complaint</th>
                            <th>Scheduled Date</th>
                            <th>Venue</th>
                            <th>Status</th>
                            <th>Outcome</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hearings as $hearing)
                        <tr>
                            <td>
                                <strong>{{ $hearing->hearing_number }}</strong>
                                @if($hearing->hearing_type)
                                    <br><small class="text-muted">{{ ucfirst($hearing->hearing_type) }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $hearing->complaint->complaint_number }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($hearing->complaint->subject, 30) }}</small>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> {{ $hearing->complaint->complainant->full_name }}
                                </small>
                            </td>
                            <td>
                                {{ $hearing->scheduled_date->format('M d, Y') }}
                                <br>
                                <small class="text-muted">{{ $hearing->scheduled_date->format('h:i A') }}</small>
                                <br>
                                @if($hearing->status === 'scheduled')
                                    @if($hearing->scheduled_date->isToday())
                                        <span class="badge bg-warning">TODAY</span>
                                    @elseif($hearing->scheduled_date->isFuture())
                                        <small class="text-muted">{{ $hearing->scheduled_date->diffForHumans() }}</small>
                                    @endif
                                @endif
                            </td>
                            <td>{{ Str::limit($hearing->venue, 30) }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'scheduled' => 'info',
                                        'ongoing' => 'warning',
                                        'completed' => 'success',
                                        'postponed' => 'secondary',
                                        'cancelled' => 'danger'
                                    ];
                                    $color = $statusColors[$hearing->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst($hearing->status) }}
                                </span>
                                @if($hearing->status === 'completed' && !$hearing->minutes)
                                    <br><span class="badge bg-danger mt-1">No Minutes</span>
                                @endif
                            </td>
                            <td>
                                @if($hearing->outcome)
                                    <span class="badge bg-{{ $hearing->outcome === 'settled' ? 'success' : 'info' }}">
                                        {{ ucfirst($hearing->outcome) }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('lupon.hearings.show', $hearing) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>No hearings found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $hearings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
