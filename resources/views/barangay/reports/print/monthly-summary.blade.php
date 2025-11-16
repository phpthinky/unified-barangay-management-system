<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Summary - {{ $date->format('F Y') }} - {{ $barangay->name }}</title>
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
        
        .revenue-highlight {
            background-color: #d4edda;
            border: 3px solid #28a745;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .revenue-highlight h3 {
            font-size: 14px;
            color: #155724;
            margin-bottom: 10px;
        }
        
        .revenue-highlight .amount {
            font-size: 32px;
            font-weight: bold;
            color: #155724;
        }
        
        .revenue-highlight .breakdown {
            margin-top: 10px;
            font-size: 11px;
            color: #155724;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .section-content {
            border: 1px solid #ddd;
            padding: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .stat-item {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .stat-item .number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-item .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-top: 5px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .summary-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .summary-table .total-row {
            background-color: #d4edda;
            font-weight: bold;
        }
        
        .insights {
            background-color: #d1ecf1;
            border-left: 4px solid #0c5460;
            padding: 15px;
            margin-top: 20px;
        }
        
        .insights h4 {
            color: #0c5460;
            margin-bottom: 10px;
            font-size: 13px;
        }
        
        .insights ul {
            margin-left: 20px;
        }
        
        .insights li {
            margin-bottom: 5px;
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
            
            .section {
                page-break-inside: avoid;
            }
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
        <h2>MONTHLY SUMMARY REPORT</h2>
        <h2>{{ strtoupper($date->format('F Y')) }}</h2>
        <p>Generated on {{ date('F d, Y h:i A') }}</p>
    </div>
    
    <!-- Total Revenue Highlight -->
    <div class="revenue-highlight">
        <h3>TOTAL REVENUE FOR {{ strtoupper($date->format('F Y')) }}</h3>
        <div class="amount">₱{{ number_format($totalRevenue, 2) }}</div>
        <div class="breakdown">
            Documents: ₱{{ number_format($documentsData['revenue'], 2) }} | 
            Business Permits: ₱{{ number_format($permitsData['revenue'], 2) }}
        </div>
    </div>
    
    <!-- Residents Section -->
    <div class="section">
        <div class="section-title">RESIDENTS</div>
        <div class="section-content">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="number">{{ number_format($residentsData['new_registrations']) }}</div>
                    <div class="label">New Registrations</div>
                </div>
                <div class="stat-item">
                    <div class="number">{{ number_format($residentsData['verified']) }}</div>
                    <div class="label">Verified</div>
                </div>
                <div class="stat-item">
                    <div class="number">-</div>
                    <div class="label">Profile Updates</div>
                </div>
            </div>
            <p style="margin-top: 10px; font-size: 11px;">
                <strong>Summary:</strong> {{ $residentsData['new_registrations'] }} new residents registered this month, 
                with {{ $residentsData['verified'] }} profiles successfully verified by barangay staff.
            </p>
        </div>
    </div>
    
    <!-- Documents Section -->
    <div class="section">
        <div class="section-title">DOCUMENT REQUESTS</div>
        <div class="section-content">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="number">{{ number_format($documentsData['total_requests']) }}</div>
                    <div class="label">Total Requests</div>
                </div>
                <div class="stat-item">
                    <div class="number">{{ number_format($documentsData['approved']) }}</div>
                    <div class="label">Approved</div>
                </div>
                <div class="stat-item">
                    <div class="number">₱{{ number_format($documentsData['revenue'], 2) }}</div>
                    <div class="label">Revenue</div>
                </div>
            </div>
            <p style="margin-top: 10px; font-size: 11px;">
                <strong>Summary:</strong> {{ $documentsData['total_requests'] }} document requests processed, 
                {{ $documentsData['approved'] }} approved and released, 
                generating ₱{{ number_format($documentsData['revenue'], 2) }} in revenue.
            </p>
        </div>
    </div>
    
    <!-- Complaints Section -->
    <div class="section">
        <div class="section-title">COMPLAINTS & DISPUTES</div>
        <div class="section-content">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="number">{{ number_format($complaintsData['total_filed']) }}</div>
                    <div class="label">Filed</div>
                </div>
                <div class="stat-item">
                    <div class="number">{{ number_format($complaintsData['resolved']) }}</div>
                    <div class="label">Resolved</div>
                </div>
                <div class="stat-item">
                    <div class="number">{{ number_format($complaintsData['hearings_held']) }}</div>
                    <div class="label">Hearings Held</div>
                </div>
            </div>
            <p style="margin-top: 10px; font-size: 11px;">
                <strong>Summary:</strong> {{ $complaintsData['total_filed'] }} complaints filed this month, 
                {{ $complaintsData['resolved'] }} successfully resolved, 
                with {{ $complaintsData['hearings_held'] }} hearings conducted.
            </p>
        </div>
    </div>
    
    <!-- Business Permits Section -->
    <div class="section">
        <div class="section-title">BUSINESS PERMITS</div>
        <div class="section-content">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="number">{{ number_format($permitsData['total_applications']) }}</div>
                    <div class="label">Applications</div>
                </div>
                <div class="stat-item">
                    <div class="number">{{ number_format($permitsData['approved']) }}</div>
                    <div class="label">Approved</div>
                </div>
                <div class="stat-item">
                    <div class="number">₱{{ number_format($permitsData['revenue'], 2) }}</div>
                    <div class="label">Revenue</div>
                </div>
            </div>
            <p style="margin-top: 10px; font-size: 11px;">
                <strong>Summary:</strong> {{ $permitsData['total_applications'] }} permit applications received, 
                {{ $permitsData['approved'] }} approved, 
                generating ₱{{ number_format($permitsData['revenue'], 2) }} in fees.
            </p>
        </div>
    </div>
    
    <!-- Financial Summary -->
    <div class="section">
        <div class="section-title">FINANCIAL SUMMARY</div>
        <div class="section-content">
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Document Request Fees</td>
                        <td style="text-align: right;">₱{{ number_format($documentsData['revenue'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Business Permit Fees</td>
                        <td style="text-align: right;">₱{{ number_format($permitsData['revenue'], 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td><strong>TOTAL REVENUE</strong></td>
                        <td style="text-align: right;"><strong>₱{{ number_format($totalRevenue, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Key Insights -->
    <div class="insights">
        <h4>KEY INSIGHTS & PERFORMANCE METRICS</h4>
        <ul>
            @if($complaintsData['resolved'] > 0 && $complaintsData['total_filed'] > 0)
            <li><strong>Complaint Resolution Rate:</strong> {{ number_format(($complaintsData['resolved'] / $complaintsData['total_filed']) * 100, 1) }}% of complaints successfully resolved</li>
            @endif
            
            @if($documentsData['approved'] > 0 && $documentsData['total_requests'] > 0)
            <li><strong>Document Approval Rate:</strong> {{ number_format(($documentsData['approved'] / $documentsData['total_requests']) * 100, 1) }}% of document requests approved</li>
            @endif
            
            @if($permitsData['approved'] > 0 && $permitsData['total_applications'] > 0)
            <li><strong>Permit Approval Rate:</strong> {{ number_format(($permitsData['approved'] / $permitsData['total_applications']) * 100, 1) }}% of business permits approved</li>
            @endif
            
            <li><strong>Total Hearings Conducted:</strong> {{ number_format($complaintsData['hearings_held']) }} hearings for dispute resolution</li>
            
            <li><strong>New Resident Registrations:</strong> {{ number_format($residentsData['new_registrations']) }} new residents registered</li>
            
            <li><strong>Service Activity:</strong> {{ number_format($documentsData['total_requests'] + $complaintsData['total_filed'] + $permitsData['total_applications']) }} total service transactions processed</li>
        </ul>
    </div>
    
    <div class="footer">
        <p>This is a computer-generated report from the Unified Barangay Management System</p>
        <p>Report Period: {{ $date->format('F Y') }} | Generated: {{ date('F d, Y h:i A') }}</p>
        <p>Barangay {{ $barangay->name }}, Municipality of {{ $barangay->municipality->name ?? 'Sablayan' }}</p>
    </div>
</body>
</html>