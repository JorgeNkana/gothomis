<?php

namespace App\Http\Controllers\Integrations\DHIS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\reports\reportsController;

class Bed_occupacy_summary extends Controller
{
	public static function getSummary(Request $request)
    {
        return (new reportsController())->getBedOccupancy($request);
	}
}