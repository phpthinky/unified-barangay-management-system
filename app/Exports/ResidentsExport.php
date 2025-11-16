<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResidentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $residents;

    public function __construct($residents)
    {
        $this->residents = $residents;
    }

    /**
     * Return collection of residents.
     */
    public function collection()
    {
        return $this->residents;
    }

    /**
     * Define Excel headings.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Last Name',
            'First Name',
            'Middle Name',
            'Suffix',
            'Email',
            'Contact Number',
            'Birth Date',
            'Age',
            'Gender',
            'Address',
            'Purok/Zone',
            'Civil Status',
            'Nationality',
            'Religion',
            'Occupation',
            'Monthly Income',
            'Educational Attainment',
            'Emergency Contact',
            'Emergency Number',
            'Residency Since',
            'Residency Type',
            'Registered Voter',
            'Precinct Number',
            'Senior Citizen',
            'PWD',
            'PWD ID Number',
            'Solo Parent',
            '4Ps Beneficiary',
            'Verified Status',
            'Verified Date',
            'Verified By',
            'Registration Date'
        ];
    }

    /**
     * Map data for each row.
     */
    public function map($resident): array
    {
        return [
            $resident->user->id,
            $resident->user->last_name,
            $resident->user->first_name,
            $resident->user->middle_name ?? '',
            $resident->user->suffix ?? '',
            $resident->user->email,
            $resident->user->contact_number ?? '',
            $resident->user->birth_date ? $resident->user->birth_date->format('Y-m-d') : '',
            $resident->user->age ?? '',
            ucfirst($resident->user->gender ?? ''),
            $resident->user->address ?? '',
            $resident->purok_zone,
            ucfirst($resident->civil_status),
            $resident->nationality,
            $resident->religion ?? '',
            $resident->occupation,
            $resident->monthly_income ? number_format($resident->monthly_income, 2) : '0.00',
            $resident->educational_attainment,
            $resident->emergency_contact_name,
            $resident->emergency_contact_number,
            $resident->residency_since ? $resident->residency_since->format('Y-m-d') : '',
            ucfirst($resident->residency_type),
            $resident->is_registered_voter ? 'Yes' : 'No',
            $resident->precinct_number ?? '',
            $resident->is_senior_citizen ? 'Yes' : 'No',
            $resident->is_pwd ? 'Yes' : 'No',
            $resident->pwd_id_number ?? '',
            $resident->is_solo_parent ? 'Yes' : 'No',
            $resident->is_4ps_beneficiary ? 'Yes' : 'No',
            $resident->is_verified ? 'Verified' : 'Pending',
            $resident->verified_at ? $resident->verified_at->format('Y-m-d H:i:s') : '',
            $resident->verifier ? $resident->verifier->full_name : '',
            $resident->created_at->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Apply styles to the Excel sheet.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (header row)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => '4472C4',
                    ],
                ],
                'font' => [
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                    'bold' => true,
                ],
            ],
        ];
    }
}