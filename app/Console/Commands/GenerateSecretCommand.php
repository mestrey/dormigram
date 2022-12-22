<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateSecretCommand extends Command
{
    protected $signature = 'generate:secret {--length=64}';
    protected $description = 'Generate secret key';

    public function handle()
    {
        $secret = Str::random($this->option('length'));
        $this->info('Generated secret: ' . $secret);
    }
}
