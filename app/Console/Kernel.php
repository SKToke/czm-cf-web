<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the components's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:update-subscription-due-amount')->daily();
    }

    /**
     * Register the commands for the components.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
