<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Residents Report - {{ $barangay->name }}</title>
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
            grid-template-columns: repeat(4, 1fr);
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
        
        .badge-primary {
            background-color: #cce5ff;
            color: #004085;
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
        <h2>RESIDENTS REPORT</h2>
        <p>Generated on {{ date('F d, Y h:i A') }}</p>
    </div>
    
    <div class="statistics">
        <div class="stat-box">
            <div class="label">Total Residents</div>
            <div class="value">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Verified</div>
            <div class="value">{{ number_format($stats['verified']) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Male</div>
            <div class="value">{{ number_format($stats['male']) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Female</div>
            <div class="value">{{ number_format($stats['female']) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">PWD</div>
            <div class="value">{{ number_format($stats['pwd']) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Senior Citizens</div>
            <div class="value">{{ number_format($stats['senior']) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Solo Parents</div>
            <div class="value">{{ number_format($stats['solo_parent']) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">4Ps Beneficiaries</div>
            <div class="value">{{ number_format($stats['4ps']) }}</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Purok</th>
                <th>Civil Status</th>
                <th>Occupation</th>
                <th>Classifications</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($residents as $index => $resident)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $resident->user->full_name }}</td>
                <td>{{ $resident->user->age ?? 'N/A' }}</td>
                <td>{{ ucfirst($resident->user->gender ?? 'N/A') }}</td>
                <td>{{ $resident->purok_zone }}</td>
                <td>{{ ucfirst($resident->civil_status) }}</td>
                <td>{{ $resident->occupation }}</td>
                <td>
                    @if($resident->is_pwd) <span class="badge badge-primary">PWD</span> @endif
                    @if($resident->is_senior_citizen) <span class="badge badge-primary">Senior</span> @endif
                    @if($resident->is_solo_parent) <span class="badge badge-primary">Solo Parent</span> @endif
                    @if($resident->is_4ps_beneficiary) <span class="badge badge-primary">4Ps</span> @endif
                </td>
                <td>
                    @if($resident->is_verified)
                        <span class="badge badge-success">Verified</span>
                    @else
                        <span class="badge badge-warning">Pending</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>This is a computer-generated report from the Unified Barangay Management System</p>
        <p>Total Records: {{ number_format($residents->count()) }}</p>
    </div>
</body>
</html>