<?php

namespace App\Http\Controllers\Integrations\DHIS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\reports\reportsController;
use DB;

class Eye_summary extends Controller
{
	public static function getSummary(Request $request)
    {
        return (new reportsController())->mtuhaEyeReports($request);
	}
}