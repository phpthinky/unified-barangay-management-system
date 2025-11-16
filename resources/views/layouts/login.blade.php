<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $barangay->name ?? $settings->municipality_name ?? config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">


                        @yield('content')

                <div class="text-center mt-4">
                    <small class="text-muted">&copy; {{ date('Y') }} {{ $settings->municipality_name ?? config('app.name') }}</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
