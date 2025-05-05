<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearSessions extends Command
{
    protected $signature = 'session:clear';
    protected $description = 'Clear all session files';

    public function handle()
    {
        $path = storage_path('framework/sessions');

        if (!file_exists($path)) {
            return $this->error('Session directory not found!');
        }

        foreach (glob("$path/*") as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $this->info('Session files cleared successfully!');
    }
}
