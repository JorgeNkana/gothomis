<?php

namespace App\Http\Controllers\Integrations\DHIS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Pharmacy\PharmacyItemsController;

class Tracer_medicine_summary extends Controller
{
	public static function getSummary(Request $request)
    {
		$found = (new PharmacyItemsController())->tracer_medicines_report($request);
		
		$summary = [];
		//customize the response to fit for DHIS fields
		foreach($found as $entry){
			$entry->service_provision = ($entry->service_provision == 1 ? true : false);
			$entry->status = ($entry->status == 1 ? true : ($entry->status == 0 ? false : null));
			$entry->stock_out_flag_a = (strtolower($entry->stock_out_flag) == 'a' ? true : null);
			$entry->stock_out_flag_b = (strtolower($entry->stock_out_flag) == 'b' ? true : null);
			$entry->stock_out_flag_c = (strtolower($entry->stock_out_flag) == 'c' ? true : null);
			$summary[] = $entry;
		}
		
		return $summary;
	}
}