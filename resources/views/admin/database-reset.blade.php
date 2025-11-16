@extends('layouts.abc')

@section('title', 'Database Reset')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Danger Zone: Database Reset</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="alert alert-danger">
                        <h5 class="alert-heading"><i class="fas fa-skull-crossbones"></i> WARNING!</h5>
                        <p><strong>This action is IRREVERSIBLE and will:</strong></p>
                        <ul>
                            <li>Drop ALL tables in the database</li>
                            <li>Delete ALL data (users, barangays, complaints, documents, etc.)</li>
                            <li>Re-run all migrations from scratch</li>
                            <li>Re-seed the database with default data</li>
                            <li>Log you out immediately</li>
                        </ul>
                        <hr>
                        <p class="mb-0"><strong>⚠️ Only use this in development/testing environments!</strong></p>
                    </div>

                    <form method="POST" action="{{ route('abc.database.reset.execute') }}" 
                          onsubmit="return confirm('⚠️ FINAL WARNING!\n\nThis will DELETE ALL DATA!\n\nAre you ABSOLUTELY SURE?');">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label">Enter Your Password to Confirm</label>
                            <input type="password" 
                                   name="confirmation_password" 
                                   class="form-control @error('confirmation_password') is-invalid @enderror" 
                                   required 
                                   placeholder="Enter your password to proceed">
                            @error('confirmation_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('abc.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel & Go Back
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt"></i> Reset Database NOW
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Reset Logs -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Check <code>storage/logs/laravel.log</code> for reset history.</p>
                    <small class="text-muted">All reset actions are logged with timestamp and user info.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card-header.bg-danger {
    border-bottom: 3px solid #dc3545;
}
</style>
@endpush