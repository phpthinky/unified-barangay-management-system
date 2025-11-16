<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbcBarangayExport implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['reports']);
    }

    public function headings(): array
    {
        return [
            'Barangay',
            'Total Residents',
            'Verified Residents',
            'Pending Residents',
            'Documents Processed',
            'Complaints Received',
            'Permits Processed',
            'Document Completion Rate',
            'Complaint Resolution Rate',
            'Permit Approval Rate',
            'Average Processing Days',
            'Rank',
            'Date Range'
        ];
    }

    public function map($report): array
    {
        return [
            $report['barangay']->name,
            $report['data']['total_residents'],
            $report['data']['verified_residents'],
            $report['data']['pending_residents'],
            $report['data']['documents_processed'],
            $report['data']['complaints_received'],
            $report['data']['permits_processed'],
            $report['metrics']['document_completion_rate'] . '%',
            $report['metrics']['complaint_resolution_rate'] . '%',
            $report['metrics']['permit_approval_rate'] . '%',
            $report['metrics']['avg_processing_days'] . ' days',
            $report['comparison']['rank']['current'] . '/' . $report['comparison']['rank']['total'],
            $this->data['dateRange']['label']
        ];
    }

    public function title(): string
    {
        return 'Barangay Reports';
    }
}