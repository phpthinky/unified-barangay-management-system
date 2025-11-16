<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receipt #{{ $documentRequest->control_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { font-size: 14px; color: #555; }
        .divider { border-top: 1px solid #ddd; margin: 15px 0; }
        .qr-container { text-align: center; margin: 20px 0; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">BARANGAY {{ strtoupper($barangay->name) }}</div>
        <div class="subtitle">{{ $barangay->municipality }}, {{ $barangay->province }}</div>
        <div class="divider"></div>
    </div>

    <div class="content">
        <div style="text-align: center; margin-bottom: 25px;">
            <div style="font-weight: bold; font-size: 16px;">DOCUMENT REQUEST RECEIPT</div>
            <div>Control #: {{ $documentRequest->control_number }}</div>
        </div>

        <div style="margin-bottom: 20px;">
            <div><strong>Resident:</strong> {{ $resident->first_name }} {{ $resident->last_name }}</div>
            <div><strong>Address:</strong> {{ $resident->street }}, Purok {{ $resident->purok }}</div>
        </div>

        <div class="divider"></div>

        <div style="margin-bottom: 20px;">
            <div><strong>Document Type:</strong> {{ ucfirst($documentRequest->type) }}</div>
            <div><strong>Purpose:</strong> {{ $documentRequest->purpose }}</div>
            <div><strong>Date Issued:</strong> {{ $dateIssued }}</div>
            <div><strong>Status:</strong> {{ ucfirst($documentRequest->status) }}</div>
        </div>

        <div class="qr-container">
            <img src="{{ $qrCodePng }}" style="width: 150px; height: 150px;">
            <div style="margin-top: 10px; font-size: 12px;">Scan to verify</div>
        </div>
    </div>

    <div class="footer">
        <div style="margin-top: 50px; border-top: 1px solid #000; width: 200px; margin-left: auto; margin-right: auto; padding-top: 5px;">
            Barangay Captain
        </div>
        <div style="margin-top: 10px; font-size: 11px;">
            This is an electronically generated document.
        </div>
    </div>
</body>
</html>