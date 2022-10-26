<?php

namespace App\Http\Controllers\Integrations\DHIS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Pediatric\Pediatric_Controller;

class DTC_summary extends Controller
{
	public static function getSummary(Request $request)
    {
      return (new Pediatric_Controller())->mtuhaDTC($request);
	}
}