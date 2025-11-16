@extends('layouts.admin')

@section('title', 'Site Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Site Settings</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="municipality_name" class="form-label">Municipality Name</label>
                                <input type="text" class="form-control @error('municipality_name') is-invalid @enderror" 
                                       id="municipality_name" name="municipality_name" 
                                       value="{{ old('municipality_name', $settings->municipality_name) }}" required>
                                @error('municipality_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="system_name" class="form-label">System Name</label>
                                <input type="text" class="form-control @error('system_name') is-invalid @enderror" 
                                       id="system_name" name="system_name" 
                                       value="{{ old('system_name', $settings->system_name) }}" required>
                                @error('system_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                       id="contact_email" name="contact_email" 
                                       value="{{ old('contact_email', $settings->contact_email) }}" required>
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_phone" class="form-label">Contact Phone</label>
                                <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" 
                                       id="contact_phone" name="contact_phone" 
                                       value="{{ old('contact_phone', $settings->contact_phone) }}" required>
                                @error('contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Municipality Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3" required>{{ old('address', $settings->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mayor_name" class="form-label">Mayor Name</label>
                                <input type="text" class="form-control @error('mayor_name') is-invalid @enderror" 
                                       id="mayor_name" name="mayor_name" 
                                       value="{{ old('mayor_name', $settings->mayor_name) }}">
                                @error('mayor_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vice_mayor_name" class="form-label">Vice Mayor Name</label>
                                <input type="text" class="form-control @error('vice_mayor_name') is-invalid @enderror" 
                                       id="vice_mayor_name" name="vice_mayor_name" 
                                       value="{{ old('vice_mayor_name', $settings->vice_mayor_name) }}">
                                @error('vice_mayor_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="municipality_logo" class="form-label">Municipality Logo</label>
                                <input type="file" class="form-control @error('municipality_logo') is-invalid @enderror" 
                                       id="municipality_logo" name="municipality_logo" accept="image/*">
                                @if($settings->municipality_logo)
                                    <div class="mt-2">
                                        <img src="{{ asset($settings->municipality_logo) }}" 
                                             alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                                @error('municipality_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="municipality_seal" class="form-label">Municipality Seal</label>
                                <input type="file" class="form-control @error('municipality_seal') is-invalid @enderror" 
                                       id="municipality_seal" name="municipality_seal" accept="image/*">
                                                                
                                @if($settings->municipality_seal)
                                    <div class="mt-2">
                                        <img src="{{ asset($settings->municipality_seal) }}" 
                                             alt="Current Seal" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif

                                @error('municipality_seal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="maintenance_mode" 
                                       name="maintenance_mode" value="1" 
                                       {{ old('maintenance_mode', $settings->maintenance_mode) ? 'checked' : '' }}>
                                <label class="form-check-label" for="maintenance_mode">
                                    Maintenance Mode
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="registration_enabled" 
                                       name="registration_enabled" value="1" 
                                       {{ old('registration_enabled', $settings->registration_enabled) ? 'checked' : '' }}>
                                <label class="form-check-label" for="registration_enabled">
                                    Registration Enabled
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="public_access_enabled" 
                                       name="public_access_enabled" value="1" 
                                       {{ old('public_access_enabled', $settings->public_access_enabled) ? 'checked' : '' }}>
                                <label class="form-check-label" for="public_access_enabled">
                                    Public Access Enabled
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
