{-- views/documents/printable.blade.php--}
<!DOCTYPE html>
<html>
<head>
    <title>{{ $documentType->name }} - {{ $documentRequest->tracking_number }}</title>
    <meta charset="utf-8">
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
        }
        
        @media screen {
            body { background: #f8f9fa; padding: 20px; }
            .document-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 14px; 
            line-height: 1.4;
        }
        
        .document-header { 
            text-align: center; 
            margin-bottom: 30px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }
        
        .barangay-seal {
            width: 100px;
            height: 100px;
            margin: 0 auto 10px;
            border: 2px solid #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            text-align: center;
        }
        
        .document-content {
            margin: 30px 0;
            text-align: justify;
        }
        
        .signature-area {
            margin-top: 80px;
            text-align: right;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin-left: auto;
            display: block;
        }
        
        .document-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .control-buttons {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .watermark {
            position: fixed;
            opacity: 0.1;
            font-size: 100px;
            transform: rotate(-45deg);
            top: 30%;
            left: 10%;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="no-print control-buttons">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Document
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Close
        </button>
        <p class="text-muted mt-2">Use Ctrl+P to print or click the print button above</p>
    </div>

    <div class="document-container">
        <!-- Barangay Letterhead -->
        <div class="document-header">
            <div class="barangay-seal">
                OFFICIAL SEAL<br>OF<br>BARANGAY
            </div>
            <h2>REPUBLIC OF THE PHILIPPINES</h2>
            <h3>PROVINCE OF OCCIDENTAL MINDORO</h3>
            <h3>MUNICIPALITY OF SABLAYAN</h3>
            <h2>BARANGAY {{ strtoupper($barangay->name) }}</h2>
        </div>

        <!-- Document Content -->
        <div class="document-content">
            <div style="text-align: center; margin-bottom: 20px;">
                <h3><u>{{ strtoupper($documentType->name) }}</u></h3>
            </div>
            
            <p>TO WHOM IT MAY CONCERN:</p>
            
            <p style="text-indent: 40px;">
                This is to certify that <strong>{{ $resident->first_name }} {{ $resident->last_name }}</strong>, 
                of legal age, {{ $resident->residentProfile->civil_status ?? 'single' }}, 
                and a resident of {{ $resident->residentProfile->purok_zone ?? 'Purok ____' }}, 
                Barangay {{ $barangay->name }}, Sablayan, Occidental Mindoro, is known to me to be a person of good moral character and a law-abiding citizen.
            </p>
            
            <p style="text-indent: 40px;">
                This certification is issued upon the request of the above-named person for <strong>{{ $documentRequest->purpose }}</strong> 
                and for whatever legal purpose it may serve.
            </p>
            
            <p style="text-indent: 40px;">
                Issued this {{ now()->format('jS') }} day of {{ now()->format('F, Y') }} at Barangay {{ $barangay->name }}, 
                Sablayan, Occidental Mindoro.
            </p>
        </div>

        <!-- Signature Area -->
        <div class="signature-area">
            <div style="margin-bottom: 60px;"></div>
            <span class="signature-line"></span>
            <p style="margin-top: 5px;">
                <strong>HON. [BARANGAY CAPTAIN'S NAME]</strong><br>
                Punong Barangay<br>
                Barangay {{ $barangay->name }}
            </p>
        </div>

        <!-- Document Footer -->
        <div class="document-footer">
            <p>Tracking Number: <strong>{{ $documentRequest->tracking_number }}</strong></p>
            <p>Document ID: {{ $documentRequest->id }}</p>
            <p>Valid until: {{ $documentRequest->expires_at?->format('F j, Y') ?? 'N/A' }}</p>
        </div>

        <!-- Watermark -->
        <div class="watermark no-print">
            OFFICIAL DOCUMENT
        </div>
    </div>

    <script>
        // Auto-print option (optional)
        @if(request()->has('autoprint'))
        window.onload = function() {
            window.print();
        }
        @endif
        
        // After printing, close window if desired
        window.onafterprint = function() {
            // Optional: close window after printing
            // window.close();
        };
    </script>
</body>
</html>