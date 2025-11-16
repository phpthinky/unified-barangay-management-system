@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Document Request #{{ $request->control_number }}</h3>
                <a href="{{ route('requests.show', [$request, 'download' => true]) }}"
   class="btn btn-primary">
   <i class="fas fa-download"></i> Download Receipt
</a>
            </div>
        </div>
        <div class="card-body">
            <!-- Your existing show page content -->
            <!-- Add a nice preview of what will be downloaded -->
            <div class="border p-4 mb-4" style="background: white;">
                @include('requests.document-preview') <!-- Create this partial for preview -->
            </div>
        </div>
    </div>
</div>
@endsection