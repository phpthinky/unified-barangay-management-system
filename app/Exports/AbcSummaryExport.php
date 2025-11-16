<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbcSummaryExport implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect([$this->data]);
    }

    public function headings(): array
    {
        return [
            'Report Type',
            'Total Barangays',
            'Total Residents',
            'Total Officials',
            'Total Services',
            'Document Completion Rate',
            'Complaint Resolution Rate',
            'Permit Approval Rate',
            'Average Processing Days',
            'On-Time Completion',
            'Citizen Satisfaction',
            'Digital Adoption',
            'Staff Efficiency',
            'Date Range'
        ];
    }

    public function map($data): array
    {
        return [
            'Executive Summary',
            $data['overview']['total_barangays'],
            $data['overview']['total_residents'],
            $data['overview']['total_officials'],
            $data['overview']['total_services'],
            $data['performance']['document_completion_rate'] . '%',
            $data['performance']['complaint_resolution_rate'] . '%',
            $data['performance']['permit_approval_rate'] . '%',
            $data['performance']['avg_processing_days'] . ' days',
            $data['serviceQuality']['on_time_completion'] . '%',
            $data['serviceQuality']['citizen_satisfaction'] . '%',
            $data['serviceQuality']['digital_adoption'] . '%',
            $data['serviceQuality']['staff_efficiency'] . '/100',
            $data['dateRange']['label']
        ];
    }

    public function title(): string
    {
        return 'Executive Summary';
    }
}