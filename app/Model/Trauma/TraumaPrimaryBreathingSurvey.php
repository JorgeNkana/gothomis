<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaPrimaryBreathingSurvey extends Model
{
    //
    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:tbl_trauma_breathing_primary_surveys",
        "user_id"   		=> "required:tbl_trauma_breathing_primary_surveys",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:tbl_trauma_breathing_primary_surveys",
        "user_id"   		=> "required:tbl_trauma_breathing_primary_surveys",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaPrimaryBreathingSurvey::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}