<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barangay;
use Illuminate\Support\Str;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = [
            [
                'name' => 'Poblacion',
                'contact_number' => '(043) 123-4567',
                'email' => 'poblacion@sablayan.gov.ph',
                'address' => 'Poblacion, Sablayan, Occidental Mindoro',
                'description' => 'The central barangay of Sablayan municipality, serving as the town center.',
                'latitude' => 12.8367,
                'longitude' => 120.9014,
                'social_media' => [
                    'facebook' => 'https://facebook.com/barangaypoblacionsablayan',
                ],
            ],
            [
                'name' => 'Batong Buhay',
                'contact_number' => '(043) 234-5678',
                'email' => 'batongbuhay@sablayan.gov.ph',
                'address' => 'Batong Buhay, Sablayan, Occidental Mindoro',
                'description' => 'A rural barangay known for its agricultural activities and natural springs.',
                'latitude' => 12.8156,
                'longitude' => 120.8892,
                'social_media' => [
                    'facebook' => 'https://facebook.com/barangaybatongbuhaysablayan',
                ],
            ],
            [
                'name' => 'Burgos',
                'contact_number' => '(043) 345-6789',
                'email' => 'burgos@sablayan.gov.ph',
                'address' => 'Burgos, Sablayan, Occidental Mindoro',
                'description' => 'A coastal barangay with fishing communities and beach resorts.',
                'latitude' => 12.8089,
                'longitude' => 120.9156,
                'social_media' => [
                    'facebook' => 'https://facebook.com/barangayburgosSablayan',
                ],
            ],
            [
                'name' => 'Claudio Salgado',
                'contact_number' => '(043) 456-7890',
                'email' => 'claudiosalgado@sablayan.gov.ph',
                'address' => 'Claudio Salgado, Sablayan, Occidental Mindoro',
                'description' => 'An inland barangay with rich agricultural lands and livestock farming.',
                'latitude' => 12.7945,
                'longitude' => 120.8734,
            ],
            [
                'name' => 'Ibud',
                'contact_number' => '(043) 567-8901',
                'email' => 'ibud@sablayan.gov.ph',
                'address' => 'Ibud, Sablayan, Occidental Mindoro',
                'description' => 'A mountainous barangay with eco-tourism potential and forest reserves.',
                'latitude' => 12.7823,
                'longitude' => 120.8567,
            ],
            [
                'name' => 'Ligaya',
                'contact_number' => '(043) 678-9012',
                'email' => 'ligaya@sablayan.gov.ph',
                'address' => 'Ligaya, Sablayan, Occidental Mindoro',
                'description' => 'A peaceful residential barangay with growing commercial establishments.',
                'latitude' => 12.8234,
                'longitude' => 120.8923,
            ],
            [
                'name' => 'Pag-asa',
                'contact_number' => '(043) 890-1234',
                'email' => 'pagasa@sablayan.gov.ph',
                'address' => 'Pag-asa, Sablayan, Occidental Mindoro',
                'description' => 'A developing barangay with mixed residential and agricultural areas.',
                'latitude' => 12.8512,
                'longitude' => 120.9089,
                'social_media' => [
                    'facebook' => 'https://facebook.com/barangaypagasasablayan',
                ],
            ],
        ];

        foreach ($barangays as $barangayData) {
            $barangay = Barangay::create([
                'name' => $barangayData['name'],
                'slug' => Str::slug($barangayData['name']),
                'contact_number' => $barangayData['contact_number'],
                'email' => $barangayData['email'],
                'address' => $barangayData['address'],
                'description' => $barangayData['description'],
                'latitude' => $barangayData['latitude'],
                'longitude' => $barangayData['longitude'],
                'social_media' => $barangayData['social_media'] ?? null,
                'is_active' => true,
            ]);

            // Generate QR code for each barangay
            $barangay->generateQrCode();
            
            echo "Created barangay: {$barangay->name} with slug: {$barangay->slug}\n";
            echo "Registration URL: {$barangay->registration_url}\n\n";
        }

        echo "Successfully created " . count($barangays) . " barangays!\n";
    }
}