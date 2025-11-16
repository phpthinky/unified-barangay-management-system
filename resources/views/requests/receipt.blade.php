<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .qr-container { text-align: center; margin: 20px 0; }
        .footer { margin-top: 30px; font-size: 12px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>BARANGAY DOCUMENT REQUEST</h2>
        <p>Official Receipt</p>
    </div>

    <div class="content">
        <p><strong>Control Number:</strong> {{ $request->control_number }}</p>
        <p><strong>Document Type:</strong> {{ ucfirst($request->type) }}</p>
        <p><strong>Date:</strong> {{ $formattedDate }}</p>
        
        <div class="qr-container">
            {!! $qrSvg !!}
            <p>Scan to verify</p>
        </div>
    </div>

    <div class="footer">
        <p>This is an official document from {{ $request->barangay->name }}</p>
    </div>
</body>
</html>