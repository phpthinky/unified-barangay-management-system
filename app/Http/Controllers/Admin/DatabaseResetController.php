<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseResetController extends Controller
{
    /**
     * Show the reset confirmation page
     */
    public function showResetPage()
    {
        // Only ABC President can access
        if (!auth()->user()->hasRole('abc-president')) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.database-reset');
    }

    /**
     * Execute database reset
     */
    public function reset(Request $request)
    {
        // Only ABC President can reset
        if (!auth()->user()->hasRole('abc-president')) {
            abort(403, 'Unauthorized access');
        }

        // Require confirmation password
        $request->validate([
            'confirmation_password' => 'required',
        ]);

        // Verify password
        if (!password_verify($request->confirmation_password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Invalid password. Reset cancelled.');
        }

        try {
            // Log the reset action
            Log::warning('Database reset initiated by: ' . auth()->user()->email);

            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Run migrate:fresh with seed
            Artisan::call('migrate:fresh', [
                '--seed' => true,
                '--force' => true,
            ]);

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Clear all caches
            Artisan::call('optimize:clear');

            Log::info('Database reset completed successfully');

            // Logout user (they'll need to re-login with new seeded credentials)
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Database reset successfully! Please login with seeded credentials.');

        } catch (\Exception $e) {
            Log::error('Database reset failed: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Reset failed: ' . $e->getMessage());
        }
    }
}