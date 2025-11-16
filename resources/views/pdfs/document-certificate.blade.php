{{-- FILE: resources/views/pdfs/document-certificate.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $docRequest->documentType->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 18px;
            margin: 5px 0;
            font-weight: normal;
        }
        .content {
            text-align: justify;
            margin: 40px 0;
            font-size: 14px;
            white-space: pre-line;
        }
        .signature-section {
            margin-top: 60px;
            text-align: right;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 200px;
            margin: 30px 0 5px auto;
        }
        .qr-section {
            position: absolute;
            bottom: 40px;
            right: 40px;
        }
        .footer {
            position: absolute;
            bottom: 20px;
            left: 40px;
            font-size: 10px;
            color: #666;
        }
        .document-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Republic of the Philippines</h1>
        <h2>{{ \App\Models\SiteSetting::get('municipality_name') }}</h2>
        <h2>{{ $barangay->name }}</h2>
    </div>

    <div class="document-title">
        {{ strtoupper($docRequest->documentType->name) }}
    </div>

    <div class="content">
        {{ $content }}
    </div>

    <div class="signature-section">
        <div class="signature-line"></div>
        <div>{{ $barangay->captain_name ?: 'Barangay Captain' }}</div>
        <div>Barangay Captain</div>
    </div>

    @if($qrCodePath)
    <div class="qr-section">
        <img src="{{ public_path($qrCodePath) }}" alt="QR Code" width="80" height="80">
    </div>
    @endif

    <div class="footer">
        Reference No.: {{ $docRequest->reference_number }} | Generated: {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
