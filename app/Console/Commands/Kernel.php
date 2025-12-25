<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ForceLogoutForgotten; // استيراد الأمر

class Kernel extends ConsoleKernel
{
    /**
     * تعريف أوامر Artisan الخاصة بالتطبيق.
     *
     * @var array
     */
    protected $commands = [
        ForceLogoutForgotten::class, // إضافة الأمر ليكون معروفاً لدى Laravel
    ];

   
    protected function schedule(Schedule $schedule): void
    {
        
        $schedule->command('attendance:force-logout')
                 ->everyMinute()
                 ->withoutOverlapping(); 
    }

    /**
     * تسجيل أوامر التطبيق.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}