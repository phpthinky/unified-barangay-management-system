<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $documentType->name }} - {{ $documentRequest->tracking_number }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
        }
        
        .header h1, .header h2, .header h3 {
            margin: 5px 0;
            font-weight: bold;
        }
        
        .header h1 {
            font-size: 14pt;
        }
        
        .header h2 {
            font-size: 13pt;
        }
        
        .header h3 {
            font-size: 12pt;
        }
        
        .document-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 30px 0;
            text-decoration: underline;
        }
        
        .content {
            text-align: justify;
            margin: 20px 0;
            text-indent: 30px;
        }
        
        .content p {
            margin: 15px 0;
        }
        
        .signatures {
            margin-top: 50px;
        }
        
        .signature-section {
            float: right;
            text-align: center;
            width: 300px;
            margin-top: 30px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 250px;
            margin: 30px auto 10px;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            font-size: 10pt;
            color: #666;
        }
        
        .qr-section {
            position: fixed;
            bottom: 20px;
            right: 20px;
            text-align: center;
        }
        
        .qr-code {
            width: 80px;
            height: 80px;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60pt;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            font-weight: bold;
        }
        
        .document-info {
            position: fixed;
            top: 20px;
            right: 20px;
            font-size: 9pt;
            color: #666;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">{{ strtoupper($barangay->name) }}</div>
    
    <!-- Document Info -->
    <div class="document-info">
        Document ID: {{ $documentRequest->tracking_number }}<br>
        Date Issued: {{ now()->format('M d, Y') }}<br>
        Valid Until: {{ $documentRequest->expires_at ? $documentRequest->expires_at->format('M d, Y') : 'Indefinite' }}
    </div>
    
    <!-- Header -->
    <div class="header">
        <h1>REPUBLIC OF THE PHILIPPINES</h1>
        <h2>PROVINCE OF OCCIDENTAL MINDORO</h2>
        <h2>MUNICIPALITY OF SABLAYAN</h2>
        <h3>{{ strtoupper($barangay->name) }}</h3>
        
        @if($barangay->address)
        <p style="margin: 10px 0; font-size: 11pt;">
            {{ $barangay->address }}
        </p>
        @endif
        
        @if($barangay->contact_number || $barangay->email)
        <p style="margin: 5px 0; font-size: 10pt;">
            @if($barangay->contact_number)
                Tel: {{ $barangay->contact_number }}
            @endif
            @if($barangay->contact_number && $barangay->email) | @endif
            @if($barangay->email)
                Email: {{ $barangay->email }}
            @endif
        </p>
        @endif
    </div>
    
    <!-- Document Title -->
    <div class="document-title">
        {{ strtoupper($documentType->name) }}
    </div>
    
    <!-- Document Content -->
    <div class="content">
        <p><strong>TO WHOM IT MAY CONCERN:</strong></p>
        
        {!! nl2br(e($content)) !!}
        
        @if($documentRequest->purpose)
        <p>This certification is issued upon the request of the above-named person for <strong>{{ strtoupper($documentRequest->purpose) }}</strong> and for whatever legal purpose it may serve.</p>
        @endif
        
        <p>Given this {{ now()->format('jS') }} day of {{ now()->format('F Y') }} at {{ $barangay->name }}, Sablayan, Occidental Mindoro.</p>
    </div>
    
    <!-- Signatures -->
    <div class="signatures clearfix">
        <div class="signature-section">
            <div class="signature-line"></div>
            <strong>
                @if($barangay->captain)
                    {{ strtoupper($barangay->captain->full_name) }}
                @else
                    BARANGAY CAPTAIN
                @endif
            </strong><br>
            <em>Punong Barangay</em>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <div style="float: left;">
            <strong>{{ config('app.name', 'UBMS') }}</strong> - Unified Barangay Management System<br>
            Municipality of Sablayan, Occidental Mindoro
        </div>
        
        <div style="float: right;">
            @if($documentRequest->copies_requested > 1)
                Copy {{ $documentRequest->copies_requested }} of {{ $documentRequest->copies_requested }}
            @else
                Original Copy
            @endif
        </div>
        
        <div style="text-align: center; clear: both; margin-top: 10px;">
            <em>"Committed to Serve with Excellence and Integrity"</em>
        </div>
    </div>
    
    <!-- QR Code -->
    @if($documentRequest->qr_code && file_exists($qrCodePath))
    <div class="qr-section">
        <img src="{{ $qrCodePath }}" alt="QR Code" class="qr-code"><br>
        <small>Scan to verify</small>
    </div>
    @endif
</body>
</html>