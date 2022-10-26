<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaPrimarySurvey extends Model
{
    //
    protected $table = "tbl_trauma_airway_primary_surveys";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:tbl_trauma_airway_primary_surveys",
        "user_id"   		=> "required:tbl_trauma_airway_primary_surveys",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:tbl_trauma_airway_primary_surveys",
        "user_id"   		=> "required:tbl_trauma_airway_primary_surveys",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaPrimarySurvey::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}