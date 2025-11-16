<!DOCTYPE html>
<html>
<head>
    <style>
        .qr-code {
            width: 150px;
            height: 150px;
            background-image: url("data:image/svg+xml,{!! $qrCode !!}");
            background-size: contain;
            background-repeat: no-repeat;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <!-- Your document content -->
    <div class="qr-code-container">
        <div class="qr-code"></div>
        <div style="text-align: center; margin-top: 10px;">
            Scan to verify: {{ $request->control_number }}
        </div>
    </div>
</body>
</html>