<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearSessions extends Command
{
    protected $signature = 'session:clear';
    protected $description = 'Clear all session files';

    public function handle(): void
    {
        $path = storage_path('framework/sessions');

        if (!file_exists($path)) {
            $this->error('Session directory not found!');
            return;
        }

        $files = glob("$path/*");
        if ($files === false) {
            $this->error('Failed to read session files');
            return;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $this->info('Session files cleared successfully!');
    }
}
