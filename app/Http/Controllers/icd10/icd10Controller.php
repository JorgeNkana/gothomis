<?php

namespace App\Http\Controllers\icd10;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class icd10Controller extends Controller
{
    public function icd10DiagnosisList()
    {
        $sql = "SELECT description, code FROM `tbl_diagnosis_descriptions` WHERE  where CODE NOT LIKE 'OP%' AND CODE NOT LIKE 'IP%' ";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
    public function icd_search(Request $request)
    {
        $request->all();
          $name = $request['name'];
         $sql = "SELECT description, code FROM `tbl_diagnosis_descriptions` WHERE  where CODE NOT LIKE 'OP%' AND CODE NOT LIKE 'IP%' AND (description LIKE '%".$name."%' OR code LIKE '%".$name."%')";
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
}