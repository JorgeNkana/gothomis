<?php

namespace App\Http\Controllers\Integrations\DHIS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\reports\reportsController;

class Anc_summary extends Controller
{
	public static function getSummary(Request $request)
    {
        $summary = [];
        $request['start']=$request->start_date;
        $request['end']=$request->end_date;
		
		$none = new \stdClass();
		$none->less_20 = 0;
		$none->above_20 = 0;
		$entries = (new reportsController())->Anti_natl_mtuha($request);
		foreach($entries as $entry)
			$summary[] = (count($entry) > 0 ? $entry[0] : $none);
		return $summary;
	}
}