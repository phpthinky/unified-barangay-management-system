<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $documentType->name }} - {{ $documentRequest->tracking_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .content { line-height: 1.6; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; }
        .qr-code { text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $barangay->name }} Barangay</h2>
        <h3>{{ $documentType->name }}</h3>
        <p>Tracking Number: {{ $documentRequest->tracking_number }}</p>
    </div>
    
    <div class="content">
        {!! nl2br(e($content)) !!}
    </div>

    
    <div class="footer">
        <p>Generated on: {{ now()->format('F d, Y \a\t g:i A') }}</p>
        <p>Barangay {{ $barangay->name }}, Sablayan, Occidental Mindoro</p>
    </div>
</body>
</html>