@extends('layouts.barangay')

@section('title', 'Municipality Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Municipality Admin Dashboard</h1>
            <p class="text-muted">Complete system overview and management</p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
               
                <a href="{{ route('barangay.reports.index') }}" class="btn btn-outline-success">
                    <i class="bi bi-file-earmark-text"></i> Reports
                </a>
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    @if(!empty($alerts))
    <div class="row mb-4">
        <div class="col">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle"></i> System Alerts
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($alerts as $alert)
                        <div class="d-flex justify-content-between align-items-center mb-2 alert alert-{{ $alert['type'] }}">
                            <div>
                                <strong>{{ $alert['title'] }}</strong><br>
                                <small class="text-muted">{{ $alert['message'] }}</small>
                            </div>
                            @if(!empty($alert['action']))
                                <a href="{{ $alert['action'] }}" class="btn btn-sm btn-outline-{{ $alert['type'] }}">
                                    {{ $alert['action_text'] ?? 'View' }}
                                </a>
                            @endif
                        </div>
                        @if(!$loop->last)<hr>@endif
                    @empty
                        <p class="text-muted">No system alerts</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Recent Business Permits -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Business Permits</h5>
                </div>
                <div class="card-body">
                    @forelse($recentPermits as $permit)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $permit->business_name }}</strong><br>
                                <small class="text-muted">{{ $permit->applicant->full_name }} - {{ $permit->barangay->name }}</small>
                            </div>
                            <span class="badge bg-{{ $permit->status_badge['class'] }}">
                                {{ $permit->status_badge['text'] }}
                            </span>
                        </div>
                        @if(!$loop->last)<hr>@endif
                    @empty
                        <p class="text-muted">No recent permits</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- (keep your Terms Expiring Soon, Stats Cards, Charts, etc. as-is) -->

</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyData = @json($monthlyData);
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [
                {
                    label: 'Documents',
                    data: monthlyData.map(item => item.documents),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Complaints',
                    data: monthlyData.map(item => item.complaints),
                    borderColor: 'rgb(255, 193, 7)',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Permits',
                    data: monthlyData.map(item => item.permits),
                    borderColor: 'rgb(40, 167, 69)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'New Residents',
                    data: monthlyData.map(item => item.residents),
                    borderColor: 'rgb(220, 53, 69)',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'System Activity Over Time' }
            },
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
@endpush
@endsection
