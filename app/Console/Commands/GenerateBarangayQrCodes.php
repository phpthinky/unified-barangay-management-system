<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barangay;
use App\Http\Controllers\Admin\BarangayController;

class GenerateBarangayQrCodes extends Command
{
    protected $signature = 'barangay:generate-qr {--all} {--barangay=}';
    protected $description = 'Generate QR codes for barangays';

    public function handle()
    {
        if ($this->option('barangay')) {
            $barangays = Barangay::where('slug', $this->option('barangay'))->get();
        } else {
            $barangays = Barangay::all();
        }
        
        if ($barangays->isEmpty()) {
            $this->error('No barangays found!');
            return 1;
        }
        
        $controller = app(BarangayController::class);
        
        $this->info("Generating QR codes for {$barangays->count()} barangay(s)...");
        
        $progressBar = $this->output->createProgressBar($barangays->count());
        $progressBar->start();
        
        $success = 0;
        $failed = 0;
        
        foreach ($barangays as $barangay) {
            try {
                $controller->generateQr($barangay);
                
                $qrFile = public_path('uploads' . DIRECTORY_SEPARATOR . 'qr-codes' . DIRECTORY_SEPARATOR . $barangay->qr_code);
                
                if (file_exists($qrFile)) {
                    $success++;
                } else {
                    $failed++;
                    $this->newLine();
                    $this->error("Failed: {$barangay->name}");
                }
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("Error for {$barangay->name}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("✓ Success: {$success}");
        if ($failed > 0) {
            $this->error("✗ Failed: {$failed}");
        }
        
        return 0;
    }
}