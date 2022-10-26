<?php

namespace App\Http\Controllers\Integrations\DHIS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Pharmacy\PharmacyItemsController;

class Ledger_summary extends Controller
{
	public static function getSummary(Request $request)
    {
        return (new PharmacyItemsController())->ledger($request);
	}
}