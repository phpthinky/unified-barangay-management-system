<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLogs extends Command
{
    protected $signature = 'logs:clear';
    protected $description = 'Clear Laravel log files';

    public function handle()
    {
        $files = glob(storage_path('logs/*.log'));

        foreach ($files as $file) {
            file_put_contents($file, '');
        }

        $this->info('Logs have been cleared!');
    }
}
