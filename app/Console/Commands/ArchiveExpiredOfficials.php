<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ArchiveExpiredOfficials extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'officials:archive-expired';

    /**
     * The console command description.
     */
    protected $description = 'Archive officials whose terms have expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for expired officials...');

        // ✅ Find officials with expired terms
        $expiredOfficials = User::whereHas('roles', function($query) {
                $query->whereIn('name', [
                    'barangay-captain',
                    'barangay-secretary',
                    'barangay-treasurer',
                    'barangay-councilor',
                    'barangay-staff'
                ]);
            })
            ->whereNotNull('term_end')
            ->where('term_end', '<', now())
            ->where('is_archived', false)
            ->get();

        if ($expiredOfficials->isEmpty()) {
            $this->info('✅ No expired officials found.');
            return 0;
        }

        $count = 0;

        foreach ($expiredOfficials as $official) {
            $official->update([
                'is_archived' => true,
                'is_active' => false,
                'archived_at' => now(),
                'archived_by' => null, // System auto-archived
            ]);

            $this->info("✅ Archived: {$official->name} (Term ended: {$official->term_end->format('Y-m-d')})");
            $count++;
        }

        $this->info("✅ Successfully archived {$count} official(s).");

        return 0;
    }
}