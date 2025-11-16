@extends('layouts.admin')

@section('title', 'QR Code Diagnostic')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">QR Code System Diagnostic</h1>

    <div class="row">
        <!-- GD Extension -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-{{ $gdInfo['installed'] ? 'success' : 'danger' }} text-white">
                    <i class="fas fa-{{ $gdInfo['installed'] ? 'check-circle' : 'times-circle' }}"></i>
                    GD Extension
                </div>
                <div class="card-body">
                    <p><strong>Status:</strong> {{ $gdInfo['installed'] ? 'Installed ✓' : 'Not Installed ✗' }}</p>
                    @if($gdInfo['installed'])
                        <p><strong>Version Info:</strong></p>
                        <pre class="bg-light p-2">{{ print_r($gdInfo['version'], true) }}</pre>
                    @else
                        <div class="alert alert-danger">
                            Install GD: <code>sudo apt-get install php-gd && sudo systemctl restart php8.2-fpm</code>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ImageMagick -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-{{ $imagickInfo['installed'] ? 'info' : 'secondary' }} text-white">
                    <i class="fas fa-{{ $imagickInfo['installed'] ? 'check-circle' : 'info-circle' }}"></i>
                    ImageMagick Extension
                </div>
                <div class="card-body">
                    <p><strong>Status:</strong> {{ $imagickInfo['installed'] ? 'Installed' : 'Not Installed' }}</p>
                    @if($imagickInfo['installed'])
                        <p><strong>Version:</strong> {{ $imagickInfo['version']['versionString'] ?? 'Unknown' }}</p>
                    @else
                        <p class="text-muted">Not required if GD is working</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Directory Permissions -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-{{ $permissions['writable'] ? 'success' : 'danger' }} text-white">
                    <i class="fas fa-{{ $permissions['writable'] ? 'folder-open' : 'lock' }}"></i>
                    Directory Permissions
                </div>
                <div class="card-body">
                    <p><strong>Path:</strong> <code>{{ $permissions['path'] }}</code></p>
                    <p><strong>Exists:</strong> {{ $permissions['exists'] ? 'Yes ✓' : 'No ✗' }}</p>
                    <p><strong>Writable:</strong> {{ $permissions['writable'] ? 'Yes ✓' : 'No ✗' }}</p>
                    
                    @if(!$permissions['exists'])
                        <div class="alert alert-warning">
                            Create directory: <code>mkdir -p {{ $permissions['path'] }}</code>
                        </div>
                    @endif
                    
                    @if(!$permissions['writable'])
                        <div class="alert alert-warning">
                            Fix permissions: <code>chmod 755 {{ $permissions['path'] }}</code>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Test Results -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-vial"></i>
                    QR Generation Tests
                </div>
                <div class="card-body">
                    @foreach($testResults as $test => $result)
                        <div class="mb-2">
                            <strong>{{ ucfirst(str_replace('_', ' ', $test)) }}:</strong>
                            <span class="badge badge-{{ str_contains($result, 'SUCCESS') ? 'success' : 'danger' }}">
                                {{ $result }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Live QR Test -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-qrcode"></i>
                    Live QR Code Test
                </div>
                <div class="card-body text-center">
                    <p>If you see a QR code below, the system is working:</p>
                    <div class="my-3">
                        {!! QrCode::size(200)->generate('https://ubms.test/test-success') !!}
                    </div>
                    <p class="text-muted">Scan this with your phone to test</p>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-lightbulb"></i>
                    Recommendations
                </div>
                <div class="card-body">
                    <h6>For Production Use:</h6>
                    <ul>
                        <li>Ensure GD extension is installed and enabled</li>
                        <li>Set proper directory permissions (755 for directories, 644 for files)</li>
                        <li>Configure proper error logging in config/qrcode.php</li>
                        <li>Consider caching QR codes to reduce generation overhead</li>
                        <li>Use SVG format for better scaling if possible</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection