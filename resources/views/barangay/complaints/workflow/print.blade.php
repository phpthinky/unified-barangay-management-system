<!DOCTYPE html>
<html>
<head>
    <title>Complaint Report - {{ $complaint->complaint_number }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 20px; }
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo { font-size: 24px; font-weight: bold; }
        h1 { margin: 10px 0; font-size: 20px; }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background: #f5f5f5;
            padding: 8px;
            font-weight: bold;
            border-left: 4px solid #333;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table td:first-child {
            font-weight: bold;
            width: 30%;
        }
        .respondent-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            background: #f9f9f9;
        }
        .verification-box {
            border: 2px solid #007bff;
            padding: 15px;
            margin: 15px 0;
            background: #e7f3ff;
        }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            margin: 40px auto 5px;
            text-align: center;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print</button>

    <div class="header">
        <div class="logo">REPUBLIC OF THE PHILIPPINES</div>
        <div>{{ $complaint->barangay->municipality->name ?? 'Municipality' }}</div>
        <h1>BARANGAY {{ strtoupper($complaint->barangay->name) }}</h1>
        <div style="margin-top: 15px; font-size: 18px; font-weight: bold;">
            COMPLAINT CASE REPORT
        </div>
    </div>

    {{-- Case Information --}}
    <div class="section">
        <div class="section-title">CASE INFORMATION</div>
        <table>
            <tr>
                <td>Case Number:</td>
                <td><strong>{{ $complaint->complaint_number }}</strong></td>
            </tr>
            <tr>
                <td>Date Filed:</td>
                <td>{{ $complaint->created_at->format('F d, Y h:i A') }}</td>
            </tr>
            <tr>
                <td>Type of Complaint:</td>
                <td>{{ $complaint->complaintType->name }}</td>
            </tr>
            <tr>
                <td>Priority:</td>
                <td style="text-transform: uppercase;">{{ $complaint->priority }}</td>
            </tr>
            <tr>
                <td>Current Status:</td>
                <td>{{ $complaint->workflow_status_label }}</td>
            </tr>
        </table>
    </div>

    {{-- Complainant Information --}}
    <div class="section">
        <div class="section-title">COMPLAINANT INFORMATION</div>
        <table>
            <tr>
                <td>Full Name:</td>
                <td>{{ $complaint->complainant->full_name }}</td>
            </tr>
            <tr>
                <td>Address:</td>
                <td>{{ $complaint->complainant->address }}</td>
            </tr>
            <tr>
                <td>Contact Number:</td>
                <td>{{ $complaint->complainant->contact_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{ $complaint->complainant->email }}</td>
            </tr>
        </table>
    </div>

    {{-- Respondent Information with Verification --}}
    <div class="section">
        <div class="section-title">RESPONDENT INFORMATION (VERIFIED BY SECRETARY)</div>
        @foreach($complaint->respondents as $index => $respondent)
        <div class="respondent-box">
            <h4>Respondent {{ $index + 1 }}</h4>
            
            @if($respondent['type'] === 'named')
                <table>
                    <tr>
                        <td>Name (from complainant):</td>
                        <td>{{ $respondent['name'] }}</td>
                    </tr>
                    @if(isset($respondent['alias']))
                    <tr>
                        <td>Alias:</td>
                        <td>{{ $respondent['alias'] }}</td>
                    </tr>
                    @endif
                </table>

                @if(isset($respondent['verified_by_secretary']) && $respondent['verified_by_secretary'])
                <div class="verification-box">
                    <strong>✓ VERIFIED BY SECRETARY</strong>
                    <table style="margin-top: 10px;">
                        <tr>
                            <td>Verified Name:</td>
                            <td>{{ $respondent['verified_name'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Verified Address:</td>
                            <td>{{ $respondent['verified_address'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Verified Contact:</td>
                            <td>{{ $respondent['verified_contact'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Verification Status:</td>
                            <td style="text-transform: uppercase;">{{ $respondent['verification_status'] ?? 'PENDING' }}</td>
                        </tr>
                        @if(isset($respondent['linked_user_id']) || isset($respondent['linked_rbi_id']))
                        <tr>
                            <td>Record Found:</td>
                            <td>
                                @if(isset($respondent['linked_user_id']))
                                    ✓ Registered Resident (User ID: {{ $respondent['linked_user_id'] }})
                                @elseif(isset($respondent['linked_rbi_id']))
                                    ✓ RBI Record (RBI ID: {{ $respondent['linked_rbi_id'] }})
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
                @endif

            @else
                <div style="background: #fff3cd; padding: 10px; margin: 10px 0;">
                    <strong>⚠️ UNKNOWN SUSPECT - Identity Not Yet Confirmed</strong>
                </div>
                <table>
                    <tr>
                        <td>Description:</td>
                        <td>{{ $respondent['description'] ?? 'No description' }}</td>
                    </tr>
                    @if(isset($respondent['identified_name']))
                    <tr>
                        <td>Identified As:</td>
                        <td>{{ $respondent['identified_name'] }}</td>
                    </tr>
                    @endif
                    @if(isset($respondent['additional_info']))
                    <tr>
                        <td>Additional Info:</td>
                        <td>{{ $respondent['additional_info'] }}</td>
                    </tr>
                    @endif
                </table>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Complaint Details --}}
    <div class="section">
        <div class="section-title">COMPLAINT DETAILS</div>
        <table>
            <tr>
                <td>Subject:</td>
                <td>{{ $complaint->subject }}</td>
            </tr>
            <tr>
                <td>Description:</td>
                <td>{{ $complaint->description }}</td>
            </tr>
            @if($complaint->incident_date)
            <tr>
                <td>Incident Date:</td>
                <td>{{ $complaint->incident_date->format('F d, Y') }}</td>
            </tr>
            @endif
            @if($complaint->incident_location)
            <tr>
                <td>Incident Location:</td>
                <td>{{ $complaint->incident_location }}</td>
            </tr>
            @endif
            @if($complaint->uploaded_files && count($complaint->uploaded_files) > 0)
            <tr>
                <td>Evidence Files:</td>
                <td>{{ count($complaint->uploaded_files) }} file(s) attached</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Secretary Notes --}}
    @if($complaint->secretary_notes)
    <div class="section">
        <div class="section-title">SECRETARY NOTES & RECOMMENDATION</div>
        <div style="padding: 15px; background: #f9f9f9; border-left: 4px solid #007bff;">
            {{ $complaint->secretary_notes }}
        </div>
        <table style="margin-top: 10px;">
            <tr>
                <td>Reviewed By:</td>
                <td>{{ $complaint->secretaryReviewer->full_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Date Reviewed:</td>
                <td>{{ $complaint->secretary_reviewed_at?->format('F d, Y h:i A') }}</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- Captain Decision Section --}}
    <div class="signature-section">
        <div class="section-title">FOR BARANGAY CAPTAIN ACTION</div>
        <div style="margin: 30px 0;">
            <label style="margin-right: 20px;">
                <input type="checkbox" style="width: 20px; height: 20px;"> APPROVE (Proceed to Summons)
            </label>
            <label>
                <input type="checkbox" style="width: 20px; height: 20px;"> DISMISS (Close Case)
            </label>
        </div>
        
        <div style="margin: 30px 0;">
            <strong>Captain's Notes/Remarks:</strong>
            <div style="border-bottom: 1px solid #000; min-height: 60px; margin-top: 10px;"></div>
        </div>

        <div class="signature-line">
            <strong>{{ auth()->user()->barangay->captain_name ?? 'BARANGAY CAPTAIN' }}</strong><br>
            Barangay Captain
        </div>
        
        <div style="text-align: center; margin-top: 10px;">
            Date: _______________
        </div>
    </div>

    <div style="margin-top: 50px; font-size: 11px; text-align: center; color: #666;">
        Generated on {{ now()->format('F d, Y h:i A') }} | {{ $complaint->barangay->name }} Barangay Management System
    </div>

    <script>
        // Auto-print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>