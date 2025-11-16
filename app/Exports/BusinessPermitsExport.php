// app/Exports/BusinessPermitsExport.php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BusinessPermitsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $permits;

    public function __construct($permits)
    {
        $this->permits = $permits;
    }

    public function collection()
    {
        return $this->permits;
    }

    public function headings(): array
    {
        return [
            'Permit Number',
            'Business Name',
            'Owner Name',
            'Business Type',
            'Business Address',
            'Contact Number',
            'Permit Type',
            'Status',
            'Total Fees',
            'Date Applied',
            'Approved Date',
            'Expires At',
            'Applicant',
        ];
    }

    public function map($permit): array
    {
        return [
            $permit->permit_number ?? 'N/A',
            $permit->business_name,
            $permit->owner_name,
            $permit->business_type,
            $permit->business_address,
            $permit->contact_number,
            $permit->businessPermitType->name,
            ucfirst($permit->status),
            '₱' . number_format($permit->total_fees, 2),
            $permit->created_at->format('Y-m-d'),
            $permit->approved_at ? $permit->approved_at->format('Y-m-d') : 'N/A',
            $permit->expires_at ? $permit->expires_at->format('Y-m-d') : 'N/A',
            $permit->applicant->name,
        ];
    }
}