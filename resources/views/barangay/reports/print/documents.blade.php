<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents Report - {{ $barangay->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            padding: 20px;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .print-header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .print-header h2 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .print-header p {
            font-size: 11px;
            color: #666;
        }
        
        .statistics {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .stat-box {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        
        .stat-box .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        
        .stat-box .value {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .revenue-box {
            grid-column: span 6;
            background-color: #d4edda;
            border: 2px solid #28a745;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .revenue-box .label {
            font-size: 12px;
            color: #155724;
            font-weight: bold;
        }
        
        .revenue-box .value {
            font-size: 24px;
            font-weight: bold;
            color: #155724;
            margin-top: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table th,
        table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 11px;
        }
        
        table td {
            font-size: 10px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .no-print {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .no-print button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 4px;
        }
        
        .no-print button:hover {
            background-color: #0056b3;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            body {
                padding: 0;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Print Report</button>
    </div>
    
    <div class="print-header">
        <h1>BARANGAY {{ strtoupper($barangay->name) }}</h1>
        <h2>Municipality of {{ $barangay->municipality->name ?? 'Sablayan' }}</h2>
        <h2>DOCUMENT REQUESTS REPORT</h2>
        <p>Generated on {{ date('F d, Y h:i A') }}</p>
    </div>
    
    <div class="statistics">
        <div class="stat-box">
            <div class="label">Total</div>
            <div class="value">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Pending</div>
            <div class="value">{{ number_format($stats['pending']) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Processing</div>
            <div class="value">{{ number_format($stats['processing']) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Approved</div>
            <div class="value">{{ number_format($stats['approved']) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Released</div>
            <div class="value">{{ number_format($stats['released']) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Rejected</div>
            <div class="value">{{ number_format($stats['rejected']) }}</div>
        </div>
        <div class="revenue-box">
            <div class="label">TOTAL REVENUE COLLECTED</div>
            <div class="value">₱{{ number_format($stats['total_revenue'], 2) }}</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tracking #</th>
                <th>Document Type</th>
                <th>Requestor</th>
                <th>Date Submitted</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Processed By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documents as $index => $doc)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $doc->tracking_number }}</td>
                <td>{{ $doc->documentType->name }}</td>
                <td>{{ $doc->user->full_name }}</td>
                <td>{{ $doc->submitted_at->format('M d, Y') }}</td>
                <td>
                    <span class="badge badge-{{ $doc->status == 'released' ? 'success' : ($doc->status == 'pending' ? 'warning' : 'info') }}">
                        {{ ucfirst($doc->status) }}
                    </span>
                </td>
                <td>₱{{ number_format($doc->amount_paid ?? 0, 2) }}</td>
                <td>{{ $doc->processor->full_name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>This is a computer-generated report from the Unified Barangay Management System</p>
        <p>Total Records: {{ number_format($documents->count()) }}</p>
    </div>
</body>
</html>