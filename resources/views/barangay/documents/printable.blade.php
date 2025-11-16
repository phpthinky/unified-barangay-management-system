{{-- FILE: resources/views/barangay/documents/printable.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>{{ $documentRequest->documentType->name }} - {{ $documentRequest->tracking_number }}</title>
    <meta charset="utf-8">
    <style>
        /* Reset for print compatibility */
        * { 
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @media print {
            body { 
                margin: 0; 
                padding: 0; 
                font-family: 'Times New Roman', Times, serif;
                font-size: 14px;
                line-height: 1.4;
            }
            
            .no-print { 
                display: none !important; 
            }
            
            .official-watermark { 
                position: absolute; 
                opacity: 0.1; 
                font-size: 120px; 
                transform: rotate(-45deg); 
                top: 30%; 
                left: 10%; 
                z-index: -1;
                color: #cccccc;
                pointer-events: none;
            }
            
            @page {
                margin: 15mm;
                size: A4;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .keep-together {
                page-break-inside: avoid;
            }
            
            .tracking-info {
                position: relative;
                bottom: auto;
                left: auto;
                right: auto;
                margin-top: 50px;
                border-top: 2px solid #000;
                padding-top: 15px;
            }
        }
        
        @media screen {
            body { 
                background: #f8f9fa; 
                padding: 20px;
                font-family: 'Times New Roman', Times, serif;
                font-size: 14px;
                line-height: 1.4;
            }
            
            .tracking-info {
                margin-top: 50px;
                border-top: 2px solid #ccc;
                padding-top: 15px;
                background: #f9f9f9;
            }
        }
        
        /* Common styles for both screen and print */
        body { 
            line-height: 1.4;
        }
        
        .official-header { 
            margin-bottom: 25px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        
        .barangay-seal {
            width: 90px;
            height: 90px;
            border: 2px solid #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 10px;
            line-height: 1.2;
            text-align: center;
            flex-shrink: 0;
        }
        
        .header-content {
            flex: 1;
            text-align: center;
            padding-top: 5px;
        }
        
        .header-title {
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        
        .header-title h2 {
            margin: 1px 0;
            font-size: 16px;
            font-weight: bold;
        }
        
        .header-title h3 {
            margin: 1px 0;
            font-size: 13px;
            font-weight: normal;
        }
        
        .control-panel {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #ddd;
        }
        
        .document-content {
            margin: 20px 0;
            text-align: justify;
            white-space: pre-line;
            line-height: 1.6;
            min-height: 300px;
        }
        
        .official-stamp-area {
            margin-top: 60px;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            margin: 0 auto;
            display: block;
        }
        
        .tracking-info {
            text-align: center;
            font-size: 11px;
            color: #000;
        }
        
        .document-container {
            max-width: 210mm; /* A4 width */
            min-height: 297mm; /* A4 height */
            margin: 0 auto;
            background: white;
            padding: 20mm; /* Match @page margin */
            position: relative;
        }
        
        .document-body {
            min-height: 200mm; /* Ensure content area */
        }
        
        .document-title {
            text-transform: uppercase;
            text-align: center;
            margin: 20px 0;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .tracking-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        
        .tracking-row {
            display: table-row;
        }
        
        .tracking-item {
            display: table-cell;
            padding: 2px 5px;
            text-align: left;
            vertical-align: top;
            width: 50%;
        }
        
        .tracking-label {
            font-weight: bold;
            white-space: nowrap;
        }
        
        .qr-section {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #666;
        }
        
        /* Simple button styles for screen view */
        .btn {
            padding: 8px 15px;
            margin: 0 5px;
            border: 1px solid #ccc;
            background: #fff;
            cursor: pointer;
            border-radius: 3px;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            border-color: #6c757d;
        }
        
        .text-muted {
            color: #6c757d !important;
        }
        
        .ms-3 {
            margin-left: 15px !important;
        }
        
        .mt-2 {
            margin-top: 10px !important;
        }
    </style>
</head>
<body>
    <!-- Control Panel for Barangay Staff -->
    <div class="control-panel no-print">
        <button onclick="window.print()" class="btn btn-success">
            Print Official Copy
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            Close
        </button>
        <span class="text-muted ms-3">Barangay Staff Use Only</span>
        
        <div class="mt-2">
            <span class="text-muted">
                Document Type: <strong>{{ $documentRequest->documentType->name }}</strong>
            </span>
        </div>
    </div>

    <!-- Official Document -->
    <div class="document-container">
        <div class="official-watermark no-print">OFFICIAL DOCUMENT</div>

        <div class="document-body">
            <div class="official-header">
                <div class="barangay-seal">
                    OFFICIAL SEAL<br>
                    BARANGAY<br>
                    {{ strtoupper($documentRequest->barangay->name) }}
                </div>
                <div class="header-content">
                    <div class="header-title">
                        <h2>REPUBLIC OF THE PHILIPPINES</h2>
                        <h3>PROVINCE OF OCCIDENTAL MINDORO</h3>
                        <h3>MUNICIPALITY OF SABLAYAN</h3>
                        <h2>BARANGAY {{ strtoupper($documentRequest->barangay->name) }}</h2>
                    </div>
                    
                    <!-- Document Title Centered Below Both Logo and Header -->
                    <h3 class="document-title"><u>{{ $documentRequest->documentType->name }}</u></h3>
                </div>
            </div>

            <div class="document-content">
                {!! $documentContent !!}
            </div>

            <div class="official-stamp-area">
                <div style="margin-bottom: 60px;"></div>
                
                <strong>{{ strtoupper($documentRequest->barangay->captain_name ?? 'BARANGAY CAPTAIN') }}</strong><br>
                Punong Barangay<br>
                Barangay {{ $documentRequest->barangay->name }}<br>
                <span class="signature-line"></span>
                <p style="margin-top: 5px; font-size: 11px;">(Signature over Printed Name)</p>
            </div>
        </div>

       
       <!-- Tracking Info - Adjusted for right-aligned QR code -->
<div class="tracking-info" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
    <div class="tracking-details" style="flex: 1; min-width: 250px;">
        <div class="tracking-grid">
            <div class="tracking-row">
                <div class="tracking-item">
                    <span class="tracking-label">Document ID:</span>
                    <span>{{ $documentRequest->tracking_number }}</span>
                </div>
                <div class="tracking-item">
                    <span class="tracking-label">Issued by:</span>
                    <span>{{ Auth::user()->name }}</span>
                </div>
            </div>
            <div class="tracking-row">
                <div class="tracking-item">
                    <span class="tracking-label">Issue Date:</span>
                    <span>{{ now()->format('M j, Y g:i A') }}</span>
                </div>
                <div class="tracking-item">
                    <span class="tracking-label">Valid until:</span>
                    <span>{{ $documentRequest->expires_at ? $documentRequest->expires_at->format('F j, Y') : 'Upon issuance' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="tracking-qr" style="text-align: right; flex-shrink: 0;">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode(route('track.request', $documentRequest->tracking_number)) }}&margin=0"
             alt="QR Code" style="width: 70px; height: 70px;">
        <p style="font-size: 9px; margin-top: 3px;">Scan QR to verify</p>
    </div>
</div>

    </div>

    <script>
        // Simple print functionality for Windows compatibility
        @if(request()->has('autoprint'))
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
        @endif
        
        // Fallback for older Windows browsers
        function safePrint() {
            try {
                window.print();
            } catch (e) {
                alert('Please use the browser\'s print function (Ctrl+P)');
            }
        }
    </script>
</body>
</html>