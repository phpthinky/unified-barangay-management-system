<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;
use Illuminate\Support\Str;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            // 1. BRGY CEDULA
            [
                'name' => 'Barangay Cedula',
                'slug' => 'brgy-cedula',
                'description' => 'Community Tax Certificate issued by the barangay',
                'category' => 'identification',
                'document_format' => 'certificate',
                'fee' => 50.00,
                'processing_days' => 1,
                'requirements' => ['Valid ID', 'Proof of Residency'],
                'form_fields' => [
                    ['name' => 'surname', 'label' => 'Surname', 'type' => 'text', 'required' => true],
                    ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'required' => true],
                    ['name' => 'middle_name', 'label' => 'Middle Name', 'type' => 'text', 'required' => false],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                    ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text', 'required' => true],
                    ['name' => 'birthday', 'label' => 'Birthday', 'type' => 'date', 'required' => true],
                    ['name' => 'citizenship', 'label' => 'Citizenship', 'type' => 'text', 'required' => true],
                    ['name' => 'civil_status', 'label' => 'Civil Status', 'type' => 'select', 'options' => ['Single', 'Married', 'Widowed', 'Separated'], 'required' => true],
                    ['name' => 'sex', 'label' => 'Sex', 'type' => 'select', 'options' => ['Male', 'Female'], 'required' => true],
                    ['name' => 'occupation', 'label' => 'Occupation/Business', 'type' => 'text', 'required' => true],
                ],
                'template_content' => "Barangay [BARANGAY], Sablayan, Occidental Mindoro\n\nName: [SURNAME], [FIRST_NAME] [MIDDLE_NAME]\nAddress: [ADDRESS]\nPlace of Birth: [PLACE_OF_BIRTH]\nDate of Birth: [BIRTHDAY]\nCitizenship: [CITIZENSHIP]\nCivil Status: [CIVIL_STATUS]\nSex: [SEX]\nOccupation: [OCCUPATION]\n\nThis certifies that the above-named person has paid the community tax for the current year.\n\nTotal Tax Paid: PHP [FEE]\n\nIssued this [DATE] at Barangay [BARANGAY].\n\nBarangay Treasurer:\n[TREASURER_NAME]",
                'is_active' => true,
                'sort_order' => 1,
            ],

            // 2. BARANGAY ID (Special - ID Card Format, Printing Disabled)
            [
                'name' => 'Barangay ID',
                'slug' => 'barangay-id',
                'description' => 'Official identification card issued by the barangay (ID CARD - Requires special printing)',
                'category' => 'identification',
                'document_format' => 'id_card',
                'format_notes' => 'ID Card size: 3.375" x 2.125". Requires PVC card printing. Must include photo and signature.',
                'enable_printing' => false, // Disabled - requires special handling
                'fee' => 100.00,
                'processing_days' => 5,
                'requirements' => ['2x2 Photo', 'Birth Certificate', 'Proof of Residency', 'Valid ID'],
                'form_fields' => [
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'required' => true],
                    ['name' => 'birthday', 'label' => 'Birthday', 'type' => 'date', 'required' => true],
                    ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                    ['name' => 'cell_no', 'label' => 'Cell. No.', 'type' => 'text', 'required' => true],
                    ['name' => 'contact_person', 'label' => 'Emergency Contact Person', 'type' => 'text', 'required' => true],
                    ['name' => 'contact_person_cell', 'label' => 'Emergency Contact Cell. No.', 'type' => 'text', 'required' => true],
                    ['name' => 'relationship', 'label' => 'Relationship', 'type' => 'text', 'required' => true],
                    ['name' => 'blood_type', 'label' => 'Blood Type (Optional)', 'type' => 'select', 'options' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'], 'required' => false],
                ],
                'template_content' => null, // ID cards have different format
                'is_active' => true,
                'sort_order' => 2,
            ],

            // 3. BARANGAY CLEARANCE
            [
                'name' => 'Barangay Clearance',
                'slug' => 'barangay-clearance',
                'description' => 'Certificate of clearance for various purposes',
                'category' => 'general',
                'document_format' => 'certificate',
                'fee' => 50.00,
                'processing_days' => 2,
                'requirements' => ['Valid ID', 'Cedula', 'Proof of Residency'],
                'form_fields' => [
                    ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'text', 'required' => true],
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'required' => true],
                    ['name' => 'birthday', 'label' => 'Birthday', 'type' => 'date', 'required' => true],
                    ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                ],
                'template_content' => "This is to certify that [NAME], born on [BIRTHDAY] at [PLACE_OF_BIRTH], and presently residing at [ADDRESS], Barangay [BARANGAY], is a bona fide resident of this barangay.\n\nBased on the records of this Barangay, the above-named person has no derogatory record and is known to be of good moral character.\n\nThis certification is issued upon request of the above-named person for the purpose of [PURPOSE].\n\nIssued this [DATE] at Barangay [BARANGAY], Municipality of Sablayan, Province of Occidental Mindoro.\n\nCertified by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 3,
            ],

            // 4. BUSINESS CLEARANCE
            [
                'name' => 'Business Clearance',
                'slug' => 'business-clearance',
                'description' => 'Clearance for business operations',
                'category' => 'business',
                'document_format' => 'certificate',
                'fee' => 200.00,
                'processing_days' => 5,
                'requirements' => ['DTI/SEC Registration', 'Barangay Clearance', 'Valid ID'],
                'form_fields' => [
                    ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'text', 'required' => true],
                    ['name' => 'business_name', 'label' => 'Business Name', 'type' => 'text', 'required' => true],
                    ['name' => 'operator_name', 'label' => 'Name of Operator', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                    ['name' => 'business_activity', 'label' => 'Business/Trade Activity', 'type' => 'text', 'required' => true],
                    ['name' => 'location', 'label' => 'Business Location', 'type' => 'textarea', 'required' => true],
                ],
                'template_content' => "This is to certify that [BUSINESS_NAME], owned/operated by [OPERATOR_NAME], located at [LOCATION], Barangay [BARANGAY], has been granted permission to operate within this barangay.\n\nThe business is engaged in [BUSINESS_ACTIVITY] and has complied with the necessary requirements set by the Barangay Council.\n\nThis clearance is valid for one year from date of issuance and may be revoked for violation of barangay ordinances.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nApproved by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 4,
            ],

            // 5. BARANGAY INDIGENCY
            [
                'name' => 'Barangay Indigency',
                'slug' => 'barangay-indigency',
                'description' => 'Certificate for indigent residents',
                'category' => 'general',
                'document_format' => 'certificate',
                'fee' => 0.00,
                'processing_days' => 1,
                'requirements' => ['Valid ID', 'Proof of Residency', 'Proof of Income'],
                'form_fields' => [
                    ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'text', 'required' => true],
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'required' => true],
                    ['name' => 'birthday', 'label' => 'Birthday', 'type' => 'date', 'required' => true],
                    ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                ],
                'template_content' => "This is to certify that [NAME], born on [BIRTHDAY] at [PLACE_OF_BIRTH], and residing at [ADDRESS], Barangay [BARANGAY], has been identified as an indigent member of this community.\n\nBased on our assessment and verification, the above-named person belongs to a low-income family and qualifies for indigency benefits and assistance programs.\n\nThis certification is issued for [PURPOSE] and whatever legal purpose it may serve.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nAttested by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 5,
            ],

            // 6. CERTIFICATE OF RESIDENCY
            [
                'name' => 'Certificate of Residency',
                'slug' => 'certificate-residency',
                'description' => 'Official certificate of residency',
                'category' => 'general',
                'document_format' => 'certificate',
                'fee' => 0.00,
                'processing_days' => 1,
                'requirements' => ['Valid ID', 'Proof of Residency'],
                'form_fields' => [
                    ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'text', 'required' => true],
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'required' => true],
                    ['name' => 'birthday', 'label' => 'Birthday', 'type' => 'date', 'required' => true],
                    ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                ],
                'template_content' => "This is to certify that [NAME], of legal age, born on [BIRTHDAY] at [PLACE_OF_BIRTH], is a resident of [ADDRESS] and has been living in this Barangay for [YEARS_RESIDING] years.\n\nThis certification is issued upon request for [PURPOSE].\n\nIssued this [DATE] at Barangay [BARANGAY], Municipality of Sablayan, Province of Occidental Mindoro.\n\nCertified by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 6,
            ],

            // 7. TRAVEL PERMIT
            [
                'name' => 'Travel Permit',
                'slug' => 'travel-permit',
                'description' => 'Permit for traveling with deceased',
                'category' => 'permit',
                'document_format' => 'certificate',
                'fee' => 100.00,
                'processing_days' => 1,
                'requirements' => ['Death Certificate', 'Valid ID', 'Barangay Clearance'],
                'form_fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                    ['name' => 'ilang_alaga', 'label' => 'Ilang Alaga', 'type' => 'text', 'required' => true],
                    ['name' => 'destination', 'label' => 'Saan Dadalhin', 'type' => 'text', 'required' => true],
                    ['name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date', 'required' => true],
                    ['name' => 'burial_date', 'label' => 'Date When to be Buried', 'type' => 'date', 'required' => true],
                    ['name' => 'requester_name', 'label' => 'Name of Who Request', 'type' => 'text', 'required' => true],
                ],
                'template_content' => "TRAVEL PERMIT FOR DECEASED\n\nThis permit is granted to [NAME] of [ADDRESS] to transport the remains of [ILANG_ALAGA] to [DESTINATION] for burial.\n\nDeceased Details:\nName: [ILANG_ALAGA]\nDate of Birth: [DATE_OF_BIRTH]\nDate of Burial: [BURIAL_DATE]\n\nRequested by: [REQUESTER_NAME]\n\nThis permit is valid only for the date specified and subject to compliance with health and safety protocols.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nAuthorized by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 7,
            ],

            // 8. BURIAL CLEARANCE
            [
                'name' => 'Burial Clearance',
                'slug' => 'burial-clearance',
                'description' => 'Clearance for burial',
                'category' => 'permit',
                'document_format' => 'certificate',
                'fee' => 50.00,
                'processing_days' => 1,
                'requirements' => ['Death Certificate', 'Valid ID'],
                'form_fields' => [
                    ['name' => 'name', 'label' => 'Name of Deceased', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                    ['name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date', 'required' => true],
                    ['name' => 'burial_date', 'label' => 'Date When to be Buried', 'type' => 'date', 'required' => true],
                ],
                'template_content' => "BURIAL CLEARANCE\n\nThis is to certify that clearance is granted for the burial of [NAME], who passed away and will be laid to rest on [BURIAL_DATE].\n\nDeceased was born on [DATE_OF_BIRTH] and was a resident of [ADDRESS], Barangay [BARANGAY].\n\nThis clearance is issued to facilitate the burial process and ensure compliance with local regulations.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nApproved by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 8,
            ],

            // 9. DEATH CERTIFICATE
            [
                'name' => 'Death Certificate',
                'slug' => 'death-certificate',
                'description' => 'Certificate of death',
                'category' => 'general',
                'document_format' => 'certificate',
                'fee' => 50.00,
                'processing_days' => 2,
                'requirements' => ['Medical Certificate of Death', 'Valid ID of Requester'],
                'form_fields' => [
                    ['name' => 'deceased_name', 'label' => 'Pangalan ng Namatay', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                    ['name' => 'date_of_death', 'label' => 'Kelan Namatay', 'type' => 'date', 'required' => true],
                    ['name' => 'requester_name', 'label' => 'Pangalan ng Nag Request', 'type' => 'text', 'required' => true],
                ],
                'template_content' => "CERTIFICATION OF DEATH\n\nThis is to certify that according to records available in this office, [DECEASED_NAME] of [ADDRESS], Barangay [BARANGAY], passed away on [DATE_OF_DEATH].\n\nThis certification is issued upon the request of [REQUESTER_NAME] for whatever legal purpose it may serve.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nCertified by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 9,
            ],

            // 10. SURVEY PERMIT
            [
                'name' => 'Survey Permit',
                'slug' => 'survey-permit',
                'description' => 'Permit for land survey',
                'category' => 'permit',
                'document_format' => 'certificate',
                'fee' => 150.00,
                'processing_days' => 3,
                'requirements' => ['Tax Declaration', 'Valid ID', 'Lot Title (if available)'],
                'form_fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                    ['name' => 'land_type', 'label' => 'Land Type', 'type' => 'select', 'options' => ['Agriculture', 'Residential'], 'required' => true],
                    ['name' => 'engineer_name', 'label' => 'Engr. Name (Magsusukat)', 'type' => 'text', 'required' => true],
                    ['name' => 'lot_location', 'label' => 'Location of Lot', 'type' => 'textarea', 'required' => true],
                    ['name' => 'lot_no', 'label' => 'Lot No.', 'type' => 'text', 'required' => false],
                    ['name' => 'title_no', 'label' => 'Title No.', 'type' => 'text', 'required' => false],
                ],
                'template_content' => "SURVEY PERMIT\n\nThis permit is granted to [NAME] of [ADDRESS] to conduct a land survey on the property located at [LOT_LOCATION].\n\nProperty Details:\nLand Type: [LAND_TYPE]\nLot No.: [LOT_NO]\nTitle No.: [TITLE_NO]\n\nSurvey to be conducted by: [ENGINEER_NAME]\n\nThis permit is valid for 30 days from date of issuance and subject to compliance with local regulations.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nApproved by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 10,
            ],

            // 11. CERTIFICATION FOR NATIONAL ID
            [
                'name' => 'Certification for National ID',
                'slug' => 'certification-national-id',
                'description' => 'Certification for National ID application',
                'category' => 'identification',
                'document_format' => 'certificate',
                'fee' => 30.00,
                'processing_days' => 1,
                'requirements' => ['Valid ID', 'Proof of Residency'],
                'form_fields' => [
                    ['name' => 'full_name', 'label' => 'Full Name', 'type' => 'text', 'required' => true],
                    ['name' => 'age', 'label' => 'Age', 'type' => 'number', 'required' => true],
                    ['name' => 'birthday', 'label' => 'Birthday', 'type' => 'date', 'required' => true],
                    ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text', 'required' => true],
                    ['name' => 'civil_status', 'label' => 'Civil Status', 'type' => 'select', 'options' => ['Single', 'Married', 'Widowed', 'Separated'], 'required' => true],
                    ['name' => 'address', 'label' => 'Address w/ Purok/Sitio', 'type' => 'textarea', 'required' => true],
                    ['name' => 'years_residing', 'label' => 'Bilang ng Taon ng Paninirahan', 'type' => 'number', 'required' => true],
                ],
                'template_content' => "CERTIFICATION FOR NATIONAL ID APPLICATION\n\nThis is to certify that [FULL_NAME], [AGE] years old, born on [BIRTHDAY] at [PLACE_OF_BIRTH], [CIVIL_STATUS], is a bona fide resident of [ADDRESS], Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nThe above-named person has been residing in this barangay for [YEARS_RESIDING] years and is known to be of good moral character.\n\nThis certification is issued to support the application for Philippine Identification System (PhilSys).\n\nIssued this [DATE] at Barangay [BARANGAY].\n\nCertified by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 11,
            ],

            // 12. SAME PERSON CERTIFICATION
            [
                'name' => 'Same Person Certification',
                'slug' => 'same-person-certification',
                'description' => 'Certification that two names refer to the same person',
                'category' => 'general',
                'document_format' => 'certificate',
                'fee' => 50.00,
                'processing_days' => 2,
                'requirements' => ['Valid IDs showing different names', 'Birth Certificate', 'Affidavit of One and the Same Person'],
                'form_fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                    ['name' => 'birthday', 'label' => 'Birthday', 'type' => 'date', 'required' => true],
                    ['name' => 'incorrect_detail', 'label' => 'Mali na Detalye na Papalitan', 'type' => 'text', 'required' => true],
                    ['name' => 'correct_detail', 'label' => 'Tama na Detalye', 'type' => 'text', 'required' => true],
                ],
                'template_content' => "CERTIFICATION OF ONE AND THE SAME PERSON\n\nThis is to certify that [NAME], born on [BIRTHDAY] and residing at [ADDRESS], Barangay [BARANGAY], is one and the same person referred to in different documents despite the discrepancy in [INCORRECT_DETAIL].\n\nThe correct information should be [CORRECT_DETAIL] as verified through our records and supporting documents.\n\nThis certification is issued to rectify the discrepancy for legal purposes.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nCertified by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 12,
            ],

            // 13. TREE CUTTING PERMIT
            [
                'name' => 'Pagpapaputol ng Kahoy',
                'slug' => 'tree-cutting-permit',
                'description' => 'Permit for tree cutting',
                'category' => 'permit',
                'document_format' => 'certificate',
                'fee' => 100.00,
                'processing_days' => 3,
                'requirements' => ['Valid ID', 'Proof of Ownership', 'DENR Permit (for large trees)'],
                'form_fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'zone', 'label' => 'Zone', 'type' => 'text', 'required' => true],
                    ['name' => 'board_feet', 'label' => 'Board Feet', 'type' => 'text', 'required' => true],
                    ['name' => 'purpose', 'label' => 'Where to Use', 'type' => 'text', 'required' => true],
                    ['name' => 'tree_kind', 'label' => 'What Kind of Tree', 'type' => 'text', 'required' => true],
                    ['name' => 'location', 'label' => 'Location', 'type' => 'textarea', 'required' => true],
                    ['name' => 'owner', 'label' => 'Owner', 'type' => 'text', 'required' => true],
                ],
                'template_content' => "TREE CUTTING PERMIT\n\nThis is to certify that [NAME], residing at [LOCATION], located in Zone [ZONE], is hereby granted permission to cut a [TREE_KIND] tree owned by [OWNER].\n\nBased on verification by the Barangay, the said tree is located within the applicant's property and the cutting is intended for [PURPOSE]. The estimated volume of the tree is [BOARD_FEET] board feet.\n\nThis certification is issued for the purpose of tree cutting as per the applicant's request, subject to compliance with existing environmental and DENR regulations.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nApproved by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 13,
            ],

            // 14. FAMILY INCOME STATUS
            [
                'name' => 'Family Income Status',
                'slug' => 'family-income-status',
                'description' => 'Certificate of family income',
                'category' => 'general',
                'document_format' => 'certificate',
                'fee' => 30.00,
                'processing_days' => 2,
                'requirements' => ['Valid ID', 'Certificate of Employment', 'ITR (if applicable)'],
                'form_fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                    ['name' => 'occupation', 'label' => 'Occupation', 'type' => 'text', 'required' => true],
                    ['name' => 'monthly_income', 'label' => 'Monthly Income', 'type' => 'number', 'required' => true],
                ],
                'template_content' => "FAMILY INCOME CERTIFICATION\n\nThis is to certify that [NAME], residing at [ADDRESS], Barangay [BARANGAY], is engaged in [OCCUPATION] with an estimated monthly income of PHP [MONTHLY_INCOME].\n\nBased on our verification, the above-named person's family belongs to the [INCOME_CLASS] income bracket.\n\nThis certification is issued for [PURPOSE] and whatever legal purpose it may serve.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nCertified by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 14,
            ],

            // 15. TREE PLANTING CERTIFICATE
            [
                'name' => 'Tree Planting Certificate',
                'slug' => 'tree-planting-certificate',
                'description' => 'Certificate for tree planting activity',
                'category' => 'general',
                'document_format' => 'certificate',
                'fee' => 50.00,
                'processing_days' => 2,
                'requirements' => ['Valid ID', 'Photos of Tree Planting'],
                'form_fields' => [
                    ['name' => 'mr_name', 'label' => 'Mr. Name', 'type' => 'text', 'required' => false],
                    ['name' => 'ms_name', 'label' => 'Ms. Name', 'type' => 'text', 'required' => false],
                    ['name' => 'purpose', 'label' => 'Where to Use', 'type' => 'text', 'required' => true],
                    ['name' => 'date', 'label' => 'When', 'type' => 'date', 'required' => true],
                ],
                'template_content' => "TREE PLANTING CERTIFICATE\n\nThis is to certify that [MR_NAME] [MS_NAME] has actively participated in the tree planting activity conducted in Barangay [BARANGAY] on [DATE].\n\nThe activity was conducted as part of our environmental conservation program and contributes to the greening initiatives of the community.\n\nThis certification is issued for [PURPOSE] and to recognize the environmental stewardship of the participant.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nCertified by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 15,
            ],

            // 16. FIRST TIME JOB SEEKER
            [
                'name' => 'First Time Job Seeker',
                'slug' => 'first-time-job-seeker',
                'description' => 'Certificate for first time job seekers (free as per DOLE)',
                'category' => 'employment',
                'document_format' => 'certificate',
                'fee' => 0.00,
                'processing_days' => 1,
                'requirements' => ['Valid ID', 'Barangay Clearance', 'Birth Certificate'],
                'form_fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'age', 'label' => 'Age', 'type' => 'number', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                    ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'text', 'required' => true],
                ],
                'template_content' => "CERTIFICATION - FIRST TIME JOB SEEKER\n\nThis is to certify that [NAME], [AGE] years old, residing at [ADDRESS], Barangay [BARANGAY], is a first-time job seeker as defined under Republic Act No. 11261 (First Time Job Seekers Assistance Act).\n\nThe above-named person has never been employed and is actively seeking employment for [PURPOSE].\n\nThis certification is issued to avail of the benefits under the said Act, including exemption from fees and charges.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nCertified by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 16,
            ],

            // 17. MARRIAGE CERTIFICATION
            [
                'name' => 'Pagpapatunay / Mag-asawa',
                'slug' => 'marriage-certification',
                'description' => 'Certification for married couples',
                'category' => 'general',
                'document_format' => 'certificate',
                'fee' => 50.00,
                'processing_days' => 2,
                'requirements' => ['Marriage Contract', 'Valid IDs', 'Barangay Clearance'],
                'form_fields' => [
                    ['name' => 'mrs_name', 'label' => 'Mrs. Name', 'type' => 'text', 'required' => true],
                    ['name' => 'mr_name', 'label' => 'Mr. Name', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'required' => true],
                    ['name' => 'year_started', 'label' => 'Year Started', 'type' => 'number', 'required' => true],
                ],
                'template_content' => "MARRIAGE CERTIFICATION\n\nThis is to certify that [MR_NAME] and [MRS_NAME], residing at [ADDRESS], Barangay [BARANGAY], are legally married and have been living together as husband and wife since [YEAR_STARTED].\n\nThe couple is known in this community to be of good moral character and are recognized as bona fide residents of this barangay.\n\nThis certification is issued for whatever legal purpose it may serve.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nCertified by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 17,
            ],

            // 18. NO LIVE-BIRTH CERTIFICATE
            [
                'name' => 'No Live-Birth Certificate',
                'slug' => 'no-live-birth-certificate',
                'description' => 'Certificate for no record of live birth',
                'category' => 'general',
                'document_format' => 'certificate',
                'fee' => 50.00,
                'processing_days' => 3,
                'requirements' => ['Valid IDs of Parents', 'Affidavit of No Record', 'Baptismal Certificate (if available)'],
                'form_fields' => [
                    ['name' => 'child_name', 'label' => 'Name of Child', 'type' => 'text', 'required' => true],
                    ['name' => 'birthday', 'label' => 'Birthday', 'type' => 'date', 'required' => true],
                    ['name' => 'place_of_birth', 'label' => 'Place of Birth', 'type' => 'text', 'required' => true],
                    ['name' => 'mother_name', 'label' => 'Name of Mother', 'type' => 'text', 'required' => true],
                    ['name' => 'father_name', 'label' => 'Name of Father', 'type' => 'text', 'required' => true],
                ],
                'template_content' => "CERTIFICATION OF NO LIVE-BIRTH RECORD\n\nThis is to certify that based on the records available in this office, there is no record of live birth registration for [CHILD_NAME] who was born on [BIRTHDAY] at [PLACE_OF_BIRTH].\n\nChild's Parents:\nMother: [MOTHER_NAME]\nFather: [FATHER_NAME]\n\nThis certification is issued to attest that the above-named child was born to the mentioned parents but does not have a registered live birth certificate with the local civil registry.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nCertified by:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 18,
            ],

            // 19. BARC (OMECO) AND RSBSA
            [
                'name' => 'BARC (OMECO) and RSBSA',
                'slug' => 'barc-omeco-rsbsa',
                'description' => 'Barangay Agrarian Reform Committee certification',
                'category' => 'agricultural',
                'document_format' => 'certificate',
                'fee' => 100.00,
                'processing_days' => 5,
                'requirements' => ['Valid ID', 'Tax Declaration', 'Sketch Plan'],
                'form_fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'zone', 'label' => 'Zone', 'type' => 'text', 'required' => true],
                    ['name' => 'lot_size', 'label' => 'Lot Size', 'type' => 'text', 'required' => true],
                    ['name' => 'location', 'label' => 'Location', 'type' => 'textarea', 'required' => true],
                    ['name' => 'years_residing', 'label' => 'How Many Years Residing', 'type' => 'number', 'required' => true],
                    ['name' => 'north_side', 'label' => 'Who is on the North Side', 'type' => 'text', 'required' => true],
                    ['name' => 'south_side', 'label' => 'Who is on the South Side', 'type' => 'text', 'required' => true],
                    ['name' => 'east_side', 'label' => 'Who is on the East Side', 'type' => 'text', 'required' => true],
                    ['name' => 'west_side', 'label' => 'Who is on the West Side', 'type' => 'text', 'required' => true],
                ],
                'template_content' => "BARC (BARANGAY AGRARIAN REFORM COMMITTEE) CERTIFICATION\n\nThis is to certify that [NAME], residing in Zone [ZONE], Barangay [BARANGAY], is the actual tiller/owner of an agricultural land with the following details:\n\nLot Size: [LOT_SIZE]\nLocation: [LOCATION]\nYears Residing: [YEARS_RESIDING] years\n\nBoundaries:\nNorth: [NORTH_SIDE]\nSouth: [SOUTH_SIDE]\nEast: [EAST_SIDE]\nWest: [WEST_SIDE]\n\nThis certification is issued for OMECO and RSBSA registration purposes.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nCertified by:\n[BARANGAY_CAPTAIN]\nPunong Barangay\n\nBARC Chairman:\n[BARC_CHAIRMAN]",
                'is_active' => true,
                'sort_order' => 19,
            ],

            // 20. DEED OF SALE
            [
                'name' => 'Kasulatan sa Bilihan',
                'slug' => 'deed-of-sale',
                'description' => 'Deed of sale document',
                'category' => 'agricultural',
                'document_format' => 'certificate',
                'fee' => 200.00,
                'processing_days' => 5,
                'requirements' => ['Valid IDs', 'Tax Declaration', 'Lot Title', 'Sketch Plan'],
                'form_fields' => [
                    ['name' => 'seller_name', 'label' => 'Name of Seller', 'type' => 'text', 'required' => true],
                    ['name' => 'zone', 'label' => 'Zone', 'type' => 'text', 'required' => true],
                    ['name' => 'lot_size', 'label' => 'Lot Size', 'type' => 'text', 'required' => true],
                    ['name' => 'location', 'label' => 'Location', 'type' => 'textarea', 'required' => true],
                    ['name' => 'sale_amount', 'label' => 'Amount of Sale', 'type' => 'number', 'required' => true],
                    ['name' => 'buyer_name', 'label' => 'Name of Buyer', 'type' => 'text', 'required' => true],
                    ['name' => 'buyer_address', 'label' => 'Buyer Address', 'type' => 'textarea', 'required' => true],
                    ['name' => 'lot_no', 'label' => 'Lot No.', 'type' => 'text', 'required' => false],
                    ['name' => 'title_no', 'label' => 'Title No.', 'type' => 'text', 'required' => false],
                ],
                'template_content' => "DEED OF ABSOLUTE SALE\n\nKNOW ALL MEN BY THESE PRESENTS:\n\nThis Deed of Absolute Sale is made and executed by [SELLER_NAME] (hereinafter referred to as the SELLER), and [BUYER_NAME] (hereinafter referred to as the BUYER).\n\nWHEREAS, the SELLER is the absolute owner of a certain parcel of land located at [LOCATION], Zone [ZONE], Barangay [BARANGAY], with the following details:\n\nLot Size: [LOT_SIZE]\nLot No.: [LOT_NO]\nTitle No.: [TITLE_NO]\n\nWHEREAS, the SELLER has offered to sell and the BUYER has agreed to buy the said property for the total consideration of PHP [SALE_AMOUNT].\n\nNOW THEREFORE, for and in consideration of the aforementioned amount, the SELLER does hereby SELL, TRANSFER, and CONVEY unto the BUYER the above-described property.\n\nIN WITNESS WHEREOF, the parties have hereunto set their hands this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nSELLER:\n[SELLER_NAME]\n\nBUYER:\n[BUYER_NAME]\n[BUYER_ADDRESS]\n\nWITNESSED BY:\n[BARANGAY_CAPTAIN]\nPunong Barangay",
                'is_active' => true,
                'sort_order' => 20,
            ],

            // 21. BARC (RSBSA)
            [
                'name' => 'BARC (RSBSA)',
                'slug' => 'barc-rsbsa',
                'description' => 'Registry System for Basic Sectors in Agriculture',
                'category' => 'agricultural',
                'document_format' => 'certificate',
                'fee' => 100.00,
                'processing_days' => 5,
                'requirements' => ['Valid ID', 'Tax Declaration', 'Sketch Plan'],
                'form_fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'zone', 'label' => 'Zone', 'type' => 'text', 'required' => true],
                    ['name' => 'lot_size', 'label' => 'Lot Size', 'type' => 'text', 'required' => true],
                    ['name' => 'location', 'label' => 'Location', 'type' => 'textarea', 'required' => true],
                    ['name' => 'years_residing', 'label' => 'How Many Years Residing', 'type' => 'number', 'required' => true],
                    ['name' => 'north_side', 'label' => 'Who is on the North Side', 'type' => 'text', 'required' => true],
                    ['name' => 'south_side', 'label' => 'Who is on the South Side', 'type' => 'text', 'required' => true],
                    ['name' => 'east_side', 'label' => 'Who is on the East Side', 'type' => 'text', 'required' => true],
                    ['name' => 'west_side', 'label' => 'Who is on the West Side', 'type' => 'text', 'required' => true],
                ],
                'template_content' => "BARC CERTIFICATION FOR RSBSA\n\nThis is to certify that [NAME], residing in Zone [ZONE], Barangay [BARANGAY], is registered in the Registry System for Basic Sectors in Agriculture (RSBSA) with the following land details:\n\nLot Size: [LOT_SIZE]\nLocation: [LOCATION]\nYears of Residence: [YEARS_RESIDING] years\n\nProperty Boundaries:\nNorth: [NORTH_SIDE]\nSouth: [SOUTH_SIDE]\nEast: [EAST_SIDE]\nWest: [WEST_SIDE]\n\nThis certification is issued to validate the agricultural land ownership/tillage for RSBSA registration and agricultural program eligibility.\n\nIssued this [DATE] at Barangay [BARANGAY], Sablayan, Occidental Mindoro.\n\nCertified by:\n[BARANGAY_CAPTAIN]\nPunong Barangay\n\nBARC Chairman:\n[BARC_CHAIRMAN]",
                'is_active' => true,
                'sort_order' => 21,
            ],
        ];

        foreach ($documentTypes as $docType) {
            DocumentType::updateOrCreate(
                ['slug' => $docType['slug']],
                $docType
            );
        }

        $this->command->info('Successfully seeded ' . count($documentTypes) . ' document types with template content!');
    }
}