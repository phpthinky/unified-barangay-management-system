<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ComplaintType;

class ComplaintTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $complaintTypes = [
            [
                'name' => 'Noise Complaint',
                'description' => 'Complaints regarding excessive noise from neighbors, businesses, or events.',
                'category' => 'barangay',
                'default_handler_type' => 'secretary',
                'requires_hearing' => false,
                'estimated_resolution_days' => 7,
                'required_information' => [
                    'Date and time of incident',
                    'Type of noise',
                    'Duration of disturbance',
                    'Respondent information',
                    'Witnesses (if any)'
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Property Boundary Dispute',
                'description' => 'Disputes over land boundaries between neighbors.',
                'category' => 'civil',
                'default_handler_type' => 'lupon',
                'requires_hearing' => true,
                'estimated_resolution_days' => 30,
                'required_information' => [
                    'Property details',
                    'Survey documents',
                    'Property titles',
                    'Dispute history',
                    'Respondent information'
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'Domestic Violence',
                'description' => 'Cases of violence within the family or household.',
                'category' => 'criminal',
                'default_handler_type' => 'captain',
                'requires_hearing' => true,
                'estimated_resolution_days' => 15,
                'required_information' => [
                    'Incident details',
                    'Medical records (if applicable)',
                    'Witness statements',
                    'Previous incidents',
                    'Protection order requests'
                ],
                'sort_order' => 3,
            ],
            [
                'name' => 'Theft/Stealing',
                'description' => 'Reports of stolen property or belongings.',
                'category' => 'criminal',
                'default_handler_type' => 'captain',
                'requires_hearing' => true,
                'estimated_resolution_days' => 20,
                'required_information' => [
                    'List of stolen items',
                    'Estimated value',
                    'Date and time of incident',
                    'Suspect information',
                    'Evidence or witnesses'
                ],
                'sort_order' => 4,
            ],
            [
                'name' => 'Verbal Altercation/Quarrel',
                'description' => 'Disputes involving verbal arguments between residents.',
                'category' => 'barangay',
                'default_handler_type' => 'lupon',
                'requires_hearing' => true,
                'estimated_resolution_days' => 10,
                'required_information' => [
                    'Cause of dispute',
                    'Parties involved',
                    'Date and time',
                    'Witnesses',
                    'Previous related incidents'
                ],
                'sort_order' => 5,
            ],
            [
                'name' => 'Physical Assault',
                'description' => 'Cases involving physical violence between individuals.',
                'category' => 'criminal',
                'default_handler_type' => 'captain',
                'requires_hearing' => true,
                'estimated_resolution_days' => 15,
                'required_information' => [
                    'Incident details',
                    'Medical certificates',
                    'Witness statements',
                    'Photos of injuries',
                    'Respondent information'
                ],
                'sort_order' => 6,
            ],
            [
                'name' => 'Debt Collection',
                'description' => 'Disputes over unpaid debts or financial obligations.',
                'category' => 'civil',
                'default_handler_type' => 'lupon',
                'requires_hearing' => true,
                'estimated_resolution_days' => 21,
                'required_information' => [
                    'Amount owed',
                    'Promissory notes',
                    'Payment history',
                    'Communication records',
                    'Collateral (if any)'
                ],
                'sort_order' => 7,
            ],
            [
                'name' => 'Animal Nuisance',
                'description' => 'Complaints about stray animals, pets causing disturbance, or animal attacks.',
                'category' => 'barangay',
                'default_handler_type' => 'secretary',
                'requires_hearing' => false,
                'estimated_resolution_days' => 5,
                'required_information' => [
                    'Type of animal',
                    'Incident details',
                    'Owner information (if known)',
                    'Date and time',
                    'Witnesses'
                ],
                'sort_order' => 8,
            ],
            [
                'name' => 'Garbage Disposal Violation',
                'description' => 'Complaints about improper waste disposal or burning of trash.',
                'category' => 'barangay',
                'default_handler_type' => 'secretary',
                'requires_hearing' => false,
                'estimated_resolution_days' => 5,
                'required_information' => [
                    'Location of violation',
                    'Date and time of incident',
                    'Type of violation',
                    'Respondent information',
                    'Photos or evidence'
                ],
                'sort_order' => 9,
            ],
            [
                'name' => 'Trespassing',
                'description' => 'Cases where individuals unlawfully enter another person’s property.',
                'category' => 'civil',
                'default_handler_type' => 'lupon',
                'requires_hearing' => true,
                'estimated_resolution_days' => 15,
                'required_information' => [
                    'Property details',
                    'Date and time of trespass',
                    'Respondent information',
                    'Witnesses',
                    'Evidence (photos, CCTV)'
                ],
                'sort_order' => 10,
            ],
            [
                'name' => 'Vandalism',
                'description' => 'Complaints regarding intentional damage to property.',
                'category' => 'criminal',
                'default_handler_type' => 'captain',
                'requires_hearing' => true,
                'estimated_resolution_days' => 10,
                'required_information' => [
                    'Description of damage',
                    'Date and time of incident',
                    'Respondent information',
                    'Witnesses',
                    'Photos or CCTV footage'
                ],
                'sort_order' => 11,
            ],
            [
                'name' => 'Curfew Violation',
                'description' => 'Complaints about individuals violating barangay curfew regulations.',
                'category' => 'barangay',
                'default_handler_type' => 'captain',
                'requires_hearing' => false,
                'estimated_resolution_days' => 3,
                'required_information' => [
                    'Name of violator',
                    'Date and time of violation',
                    'Location',
                    'Witnesses',
                    'Barangay ordinance reference'
                ],
                'sort_order' => 12,
            ],
            [
                'name' => 'Illegal Gambling',
                'description' => 'Reports of unauthorized gambling activities within the barangay.',
                'category' => 'criminal',
                'default_handler_type' => 'captain',
                'requires_hearing' => true,
                'estimated_resolution_days' => 20,
                'required_information' => [
                    'Type of gambling',
                    'Location',
                    'Date and time of activity',
                    'Respondent information',
                    'Evidence or witnesses'
                ],
                'sort_order' => 13,
            ],
            [
                'name' => 'Public Disturbance',
                'description' => 'Complaints about unruly behavior in public places.',
                'category' => 'barangay',
                'default_handler_type' => 'captain',
                'requires_hearing' => false,
                'estimated_resolution_days' => 5,
                'required_information' => [
                    'Nature of disturbance',
                    'Date and time',
                    'Location',
                    'Respondent information',
                    'Witnesses'
                ],
                'sort_order' => 14,
            ],
            [
                'name' => 'Water Supply Dispute',
                'description' => 'Complaints regarding shared water supply usage or disputes.',
                'category' => 'civil',
                'default_handler_type' => 'lupon',
                'requires_hearing' => true,
                'estimated_resolution_days' => 15,
                'required_information' => [
                    'Source of water',
                    'Details of dispute',
                    'Parties involved',
                    'History of issue',
                    'Supporting documents'
                ],
                'sort_order' => 15,
            ],

        ];

        foreach ($complaintTypes as $complaintTypeData) {
            ComplaintType::create($complaintTypeData);
            echo "Created complaint type: {$complaintTypeData['name']}\n";
        }

        echo "Successfully created " . count($complaintTypes) . " complaint types!\n";
    }
}
