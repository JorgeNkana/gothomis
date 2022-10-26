<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaPrimaryFastSurvey extends Model
{
    //
    protected $table = "trauma_primary_fast_surveys";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:trauma_primary_fast_surveys",
        "user_id"   		=> "required:trauma_primary_fast_surveys",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:trauma_primary_fast_surveys",
        "user_id"   		=> "required:trauma_primary_fast_surveys",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaPrimaryFastSurvey::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}