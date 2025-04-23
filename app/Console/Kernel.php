<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendAbandonedCartReminders;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Limpiar carritos de invitados abandonados cada semana
        $schedule->command('carts:clear-abandoned --days=30')
                ->weekly()
                ->mondays()
                ->at('01:00')
                ->appendOutputTo(storage_path('logs/carts-cleanup.log'));

        // Enviar recordatorios de carritos abandonados diariamente
        $schedule->job(new SendAbandonedCartReminders)
                ->dailyAt('10:00')
                ->environments(['production']);
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}