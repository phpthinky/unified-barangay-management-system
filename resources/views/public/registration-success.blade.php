{{-- FILE: resources/views/public/registration-success.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Success - {{ $barangay->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('public.barangay', $barangay->slug) }}">
               
@if($siteSettings && $siteSettings->municipality_logo)
    <img src="{{ asset($siteSettings->municipality_logo) }}" alt="Logo" height="30" class="me-2">
@endif
                {{ $barangay->name }}
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow text-center">
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle fa-5x text-success"></i>
                        </div>
                        
                        <h2 class="text-success mb-4">Registration Successful!</h2>
                        
                        <p class="lead mb-4">
                            Thank you for registering as a resident of <strong>{{ $barangay->name }}</strong>.
                        </p>
                        
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle me-2"></i>What happens next?</h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">1. Your registration will be reviewed by barangay officials</li>
                                <li class="mb-2">2. You will receive an email confirmation once verified</li>
                                <li class="mb-2">3. After verification, you can login and access barangay services</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('public.barangay', $barangay->slug) }}" class="btn btn-primary me-3">
                                <i class="fas fa-home me-2"></i>Back to Barangay Page
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-success">
                                <i class="fas fa-sign-in-alt me-2"></i>Login (After Verification)
                            </a>
                        </div>
                        
                        <hr class="my-4">
                        
                        <p class="text-muted">
                            <small>
                                For questions about your registration, please contact the barangay office at 
                                {{ $barangay->contact_number ?? $siteSettings->contact_phone }}.
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
