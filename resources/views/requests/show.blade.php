<!DOCTYPE html>
<html>
<head>
    <title>Document Request - {{ $request->control_number }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .qr-container {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 5px;
        }
        .info-card {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        .actions {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0 10px;
        }
        .btn.print {
            background: #2196F3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Document Request Receipt</h2>
        <p>Barangay {{ $request->barangay->name }}</p>
    </div>

    <div class="info-card">
        <div class="info-row">
            <div class="info-label">Control Number:</div>
            <div>{{ $request->control_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Document Type:</div>
            <div>{{ ucfirst(str_replace('_', ' ', $request->type)) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Request Date:</div>
            <div>{{ $request->created_at->format('M d, Y h:i A') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div>{{ ucfirst($request->status) }}</div>
        </div>
    </div>

    <div class="qr-container">
        <h3>Verification QR Code</h3>
        {!! $qrCode !!}
        <p>Scan this code to verify the document</p>
    </div>

    <div class="actions">
        <a href="{{ route('requests.download-qr', $request) }}" class="btn">Download QR Code</a>
        <button onclick="window.print()" class="btn print">Print Receipt</button>
    </div>
</body>
</html>