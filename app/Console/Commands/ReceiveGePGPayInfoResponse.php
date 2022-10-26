<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReceiveGePGPayInfoResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gepg:ReceiveGePGPayInfoResponse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $get_payinfo_from_gepg = new \App\Http\Controllers\Integrations\GePG\gepgPmtSpInfoDeamon();

		$get_payinfo_from_gepg->listen();
    }
}