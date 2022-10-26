<?php

namespace App\Http\Controllers\Payments;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

class financeControlsController extends Controller
{
    public function getCancelledBills(Request $request)
    {
		$facility_id = $request->input('facility_id');
		$start = $request->input('start');
        $end = $request->input('end');
		
        return DB::select("SELECT * FROM `vw_cancelled_bills` WHERE date BETWEEN '$start'  AND '$end' AND facility_id = $facility_id");
    }
	
}