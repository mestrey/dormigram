<?php

namespace App\Console;

use App\Console\Commands\Cron\AuthRemoveExpiredRefreshTokensCommand;
use App\Console\Commands\GenerateSecretCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        GenerateSecretCommand::class,
        AuthRemoveExpiredRefreshTokensCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(AuthRemoveExpiredRefreshTokensCommand::class)->weekly();
    }
}
