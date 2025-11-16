<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Official Receipt - {{ $request->control_number }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { height: 80px; }
        .title { font-size: 18px; font-weight: bold; margin: 5px 0; }
        .subtitle { font-size: 14px; color: #555; }
        .content { margin: 25px 0; }
        .section { margin-bottom: 15px; }
        .label { font-weight: bold; color: #333; }
        .value { margin-left: 10px; }
        .divider { border-top: 2px solid #eee; margin: 20px 0; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; }
        .signature { margin-top: 60px; }
        .qr-container { text-align: center; margin: 20px 0; }
        .official-stamp { height: 80px; opacity: 0.8; }
        .verification { background: #f9f9f9; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">BARANGAY {{ strtoupper($barangay->name) }}</div>
        <div class="subtitle">{{ $barangay->municipality }}, {{ $barangay->province }}</div>
        <div class="divider"></div>
    </div>

    <div class="content">
        <div class="section" style="text-align: center; margin-bottom: 25px;">
            <div style="font-size: 16px; font-weight: bold;">OFFICIAL DOCUMENT RECEIPT</div>
            <div style="font-size: 14px;">Control Number: {{ $request->control_number }}</div>
        </div>

        <div class="section">
            <div class="label">Resident Information:</div>
            <div class="value">{{ $resident->first_name }} {{ $resident->last_name }}</div>
            <div class="value">{{ $resident->house_number }} {{ $resident->street }}, Purok {{ $resident->purok }}</div>
        </div>

        <div class="divider"></div>

        <div class="section">
            <div class="label">Document Details:</div>
            <table width="100%" cellpadding="5">
                <tr>
                    <td width="30%">Type:</td>
                    <td>{{ ucfirst($request->type) }}</td>
                </tr>
                <tr>
                    <td>Purpose:</td>
                    <td>{{ $request->purpose }}</td>
                </tr>
                <tr>
                    <td>Date Requested:</td>
                    <td>{{ $dateIssued }}</td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td style="color: {{ $request->status === 'approved' ? 'green' : ($request->status === 'rejected' ? 'red' : 'orange') }}">
                        {{ ucfirst($request->status) }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>

        <div class="verification">
            <div class="qr-container">
                {!! $qrCodeBase64  !!}
                <div style="margin-top: 10px; font-size: 12px;">Scan to verify</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div style="margin-bottom: 20px;">
           {{--
            <img src="data:image/png;base64,{{ $officialStamp }}" class="official-stamp">
        --}}
        </div>
        <div class="signature">
            <div style="border-top: 1px solid #000; width: 200px; margin: 0 auto; padding-top: 5px;">
                Barangay Captain's Signature
            </div>
        </div>
        <div style="margin-top: 20px; font-size: 11px;">
            This is an electronically generated document. No signature is required.
        </div>
    </div>
</body>
</html>