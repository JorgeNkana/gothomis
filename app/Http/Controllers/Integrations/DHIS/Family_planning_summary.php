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
		$none->between_10_14 = 0;
		$none->between_15_19 = 0;
		$none->between_20_24 = 0; 
		$none->above_25 = 0;
		$entries = (new reportsController())->mtuhaFamilyPlanningReports($request);
		foreach($entries as $entry)
			$summary[] = (count($entry) > 0 ? $entry[0] : $none);
		return $summary;
	}
}