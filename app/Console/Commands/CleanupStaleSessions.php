<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CleanupStaleSessions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sessions:cleanup';

    /**
     * The console command description.
     */
    protected $description = 'Cleanup stale user sessions (inactive for more than 10 minutes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for stale sessions...');

        // ✅ Find users logged in but inactive for > 10 minutes
        $staleUsers = User::where('is_logged_in', true)
            ->whereNotNull('session_token')
            ->where(function($query) {
                $query->where('last_activity_at', '<', now()->subMinutes(10))
                      ->orWhereNull('last_activity_at');
            })
            ->get();

        if ($staleUsers->isEmpty()) {
            $this->info('✅ No stale sessions found.');
            return 0;
        }

        $count = 0;

        foreach ($staleUsers as $user) {
            $user->update([
                'is_logged_in' => false,
                'session_token' => null,
            ]);

            $lastActivity = $user->last_activity_at 
                ? $user->last_activity_at->diffForHumans() 
                : 'never';

            $this->info("✅ Cleaned up session for: {$user->name} (Last activity: {$lastActivity})");
            $count++;
        }

        $this->info("✅ Successfully cleaned up {$count} stale session(s).");

        return 0;
    }
}