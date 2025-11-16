<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessPermitType;

class BusinessPermitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businessPermitTypes = [
            [
                'name' => 'Sari-Sari Store',
                'description' => 'Small neighborhood retail store selling basic commodities.',
                'category' => 'micro',
                'base_fee' => 500.00,
                'processing_days' => 3,
                'validity_months' => 12,
                'requirements' => [
                    'Barangay Business Clearance',
                    'Valid ID of Owner',
                    'Proof of Store Location',
                    'List of Products to be Sold',
                    '2x2 Photo (2 pieces)',
                    'Sketch of Store Location'
                ],
                'additional_fees' => [
                    'Sanitary Permit' => 100.00,
                    'Fire Safety Inspection' => 150.00
                ],
                'requires_inspection' => true,
                'requires_fire_safety' => false,
                'requires_health_permit' => true,
                'requires_environmental_clearance' => false,
                'template_content' => 'This is to certify that [BUSINESS_NAME] owned by [OWNER_NAME] located at [BUSINESS_ADDRESS] is hereby granted permission to operate as a Sari-Sari Store.',
                'template_fields' => [
                    'BUSINESS_NAME' => 'Business Name',
                    'OWNER_NAME' => 'Owner Name',
                    'BUSINESS_ADDRESS' => 'Business Address',
                    'PERMIT_NUMBER' => 'Permit Number',
                    'ISSUE_DATE' => 'Date Issued',
                    'EXPIRY_DATE' => 'Expiry Date'
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Carinderia/Eatery',
                'description' => 'Small food establishment serving cooked meals.',
                'category' => 'small',
                'base_fee' => 1000.00,
                'processing_days' => 7,
                'validity_months' => 12,
                'requirements' => [
                    'Barangay Business Clearance',
                    'Sanitary Permit',
                    'Health Certificate of Owner/Cook',
                    'Valid ID of Owner',
                    'Proof of Business Location',
                    'Menu/List of Food Items',
                    'Kitchen Layout Plan',
                    '2x2 Photo (2 pieces)'
                ],
                'additional_fees' => [
                    'Health Department Permit' => 300.00,
                    'Fire Safety Inspection' => 200.00,
                    'LGU Food Handler Seminar' => 150.00
                ],
                'requires_inspection' => true,
                'requires_fire_safety' => true,
                'requires_health_permit' => true,
                'requires_environmental_clearance' => false,
                'template_content' => 'This is to certify that [BUSINESS_NAME] owned by [OWNER_NAME] located at [BUSINESS_ADDRESS] is hereby granted permission to operate as a Food Establishment.',
                'template_fields' => [
                    'BUSINESS_NAME' => 'Business Name',
                    'OWNER_NAME' => 'Owner Name',
                    'BUSINESS_ADDRESS' => 'Business Address',
                    'PERMIT_NUMBER' => 'Permit Number',
                    'ISSUE_DATE' => 'Date Issued',
                    'EXPIRY_DATE' => 'Expiry Date'
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'Beauty Salon/Barbershop',
                'description' => 'Establishment providing hair and beauty services.',
                'category' => 'small',
                'base_fee' => 800.00,
                'processing_days' => 5,
                'validity_months' => 12,
                'requirements' => [
                    'Barangay Business Clearance',
                    'Sanitary Permit',
                    'Professional License of Operator',
                    'Valid ID of Owner',
                    'Proof of Business Location',
                    'List of Services Offered',
                    '2x2 Photo (2 pieces)'
                ],
                'additional_fees' => [
                    'Health Department Inspection' => 200.00,
                    'Professional Regulation Commission Fee' => 100.00
                ],
                'requires_inspection' => true,
                'requires_fire_safety' => false,
                'requires_health_permit' => true,
                'requires_environmental_clearance' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Home-Based Business',
                'description' => 'Business operated from residential premises.',
                'category' => 'home_based',
                'base_fee' => 300.00,
                'processing_days' => 3,
                'validity_months' => 12,
                'requirements' => [
                    'Barangay Business Clearance',
                    'Homeowner\'s Consent (if renting)',
                    'Valid ID of Owner',
                    'Description of Business Activities',
                    'Proof of Residence',
                    '2x2 Photo (2 pieces)'
                ],
                'additional_fees' => [],
                'requires_inspection' => false,
                'requires_fire_safety' => false,
                'requires_health_permit' => false,
                'requires_environmental_clearance' => false,
                'template_content' => 'This is to certify that [BUSINESS_NAME] owned by [OWNER_NAME] located at [BUSINESS_ADDRESS] is hereby granted permission to operate as a Home-Based Business.',
                'template_fields' => [
                    'BUSINESS_NAME' => 'Business Name',
                    'OWNER_NAME' => 'Owner Name',
                    'BUSINESS_ADDRESS' => 'Business Address',
                    'BUSINESS_TYPE' => 'Type of Business'
                ],
                'sort_order' => 4,
            ],
            [
                'name' => 'Street Vendor',
                'description' => 'Mobile vendor selling goods in public areas.',
                'category' => 'street_vendor',
                'base_fee' => 200.00,
                'processing_days' => 2,
                'validity_months' => 12,
                'requirements' => [
                    'Barangay Business Clearance',
                    'Medical Certificate',
                    'Valid ID',
                    'List of Products to be Sold',
                    'Designated Vending Area Assignment',
                    '1x1 Photo (2 pieces)'
                ],
                'additional_fees' => [
                    'Daily Vending Fee' => 10.00
                ],
                'requires_inspection' => false,
                'requires_fire_safety' => false,
                'requires_health_permit' => true,
                'requires_environmental_clearance' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'Repair Shop',
                'description' => 'Workshop for repairing electronics, appliances, or vehicles.',
                'category' => 'small',
                'base_fee' => 1200.00,
                'processing_days' => 7,
                'validity_months' => 12,
                'requirements' => [
                    'Barangay Business Clearance',
                    'Technical Skills Certificate',
                    'Valid ID of Owner/Technician',
                    'Proof of Business Location',
                    'Equipment List',
                    'Insurance Policy',
                    '2x2 Photo (2 pieces)'
                ],
                'additional_fees' => [
                    'Fire Safety Inspection' => 250.00,
                    'Environmental Compliance' => 200.00
                ],
                'requires_inspection' => true,
                'requires_fire_safety' => true,
                'requires_health_permit' => false,
                'requires_environmental_clearance' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Internet Cafe/Computer Shop',
                'description' => 'Establishment providing computer and internet services.',
                'category' => 'small',
                'base_fee' => 1500.00,
                'processing_days' => 5,
                'validity_months' => 12,
                'requirements' => [
                    'Barangay Business Clearance',
                    'Fire Safety Inspection Certificate',
                    'Valid ID of Owner',
                    'Proof of Business Location',
                    'Computer Equipment List',
                    'Internet Service Provider Contract',
                    'Floor Plan',
                    '2x2 Photo (2 pieces)'
                ],
                'additional_fees' => [
                    'Fire Safety Certificate' => 300.00,
                    'Electrical Safety Inspection' => 200.00
                ],
                'requires_inspection' => true,
                'requires_fire_safety' => true,
                'requires_health_permit' => false,
                'requires_environmental_clearance' => false,
                'sort_order' => 7,
            ],
            [
                'name' => 'Retail Store',
                'description' => 'General retail establishment selling various merchandise.',
                'category' => 'medium',
                'base_fee' => 2000.00,
                'processing_days' => 10,
                'validity_months' => 12,
                'requirements' => [
                    'Barangay Business Clearance',
                    'DTI Business Registration',
                    'BIR Registration',
                    'Valid ID of Owner',
                    'Lease Contract/Property Title',
                    'Floor Plan',
                    'Product Catalog',
                    '2x2 Photo (2 pieces)'
                ],
                'additional_fees' => [
                    'Fire Safety Inspection' => 400.00,
                    'Building Permit Review' => 300.00,
                    'Signage Permit' => 150.00
                ],
                'requires_inspection' => true,
                'requires_fire_safety' => true,
                'requires_health_permit' => false,
                'requires_environmental_clearance' => false,
                'sort_order' => 8,
            ],
            [
                'name' => 'Bakery',
                'description' => 'Establishment for baking and selling bread and pastries.',
                'category' => 'small',
                'base_fee' => 1800.00,
                'processing_days' => 10,
                'validity_months' => 12,
                'requirements' => [
                    'Barangay Business Clearance',
                    'Sanitary Permit',
                    'Health Certificate of Baker/Staff',
                    'FDA License to Operate',
                    'Valid ID of Owner',
                    'Proof of Business Location',
                    'Production Area Layout',
                    'Product List',
                    '2x2 Photo (2 pieces)'
                ],
                'additional_fees' => [
                    'Health Department Permit' => 400.00,
                    'Fire Safety Inspection' => 300.00,
                    'FDA Registration' => 500.00
                ],
                'requires_inspection' => true,
                'requires_fire_safety' => true,
                'requires_health_permit' => true,
                'requires_environmental_clearance' => false,
                'sort_order' => 9,
            ],
            [
                'name' => 'Piggery/Livestock',
                'description' => 'Farm business for raising pigs or other livestock.',
                'category' => 'medium',
                'base_fee' => 2500.00,
                'processing_days' => 14,
                'validity_months' => 12,
                'requirements' => [
                    'Barangay Business Clearance',
                    'Environmental Compliance Certificate',
                    'Veterinary Health Certificate',
                    'Valid ID of Owner',
                    'Land Title/Lease Agreement',
                    'Farm Layout Plan',
                    'Waste Management Plan',
                    'Neighbor\'s Consent',
                    '2x2 Photo (2 pieces)'
                ],
                'additional_fees' => [
                    'Environmental Impact Assessment' => 1000.00,
                    'Veterinary Inspection' => 500.00,
                    'Zoning Compliance' => 300.00
                ],
                'requires_inspection' => true,
                'requires_fire_safety' => false,
                'requires_health_permit' => false,
                'requires_environmental_clearance' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($businessPermitTypes as $permitTypeData) {
            BusinessPermitType::create($permitTypeData);
            echo "Created business permit type: {$permitTypeData['name']}\n";
        }

        echo "Successfully created " . count($businessPermitTypes) . " business permit types!\n";
    }
}