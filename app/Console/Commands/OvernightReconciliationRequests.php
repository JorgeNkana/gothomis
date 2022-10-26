<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

class OvernightReconciliationRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gepg:OvernightReconciliationRequests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calls out onto GePG to collect reconcilliations that may not have been pulled by clients';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $handle = new \App\Http\Controllers\Integrations\GePG\FacilityRequestsHandler();
		$handle->sendBatchedReconcilliationRequests();
    }
}