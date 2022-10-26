<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\SendFacilityBillRequests',
        'App\Console\Commands\ReceiveGePGBillSubResponse',
        'App\Console\Commands\ReceiveGePGPayInfoResponse',
        'App\Console\Commands\ReceiveGePGReconResponse',
        'App\Console\Commands\SendFacilityReconRequests',
        'App\Console\Commands\OvernightReconciliationRequests',
		
		//test
		/*
        'App\Console\Commands\GLite\SendGothomisLiteBillRequests',
        'App\Console\Commands\GLite\ReceiveGothomisLiteGePGBillSubResponse',
        'App\Console\Commands\GLite\ReceiveGothomisLiteGePGPayInfoResponse',
        'App\Console\Commands\GLite\ReceiveGothomisLiteGePGReconResponse',
        'App\Console\Commands\GLite\SendGothomisLiteReconRequests',
		*/
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}