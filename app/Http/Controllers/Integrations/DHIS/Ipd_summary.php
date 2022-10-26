<?php

namespace App\Http\Controllers\Integrations\DHIS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\reports\reportsController;

class Ipd_summary extends Controller
{
	public static function getSummary(Request $request)
    {
        return (new reportsController())->getIpdReport($request);
	}
}