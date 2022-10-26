<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaLabResult extends Model
{
    //

    protected $table = "trauma_lab_results";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:trauma_lab_results",
        "user_id"   		=> "required:trauma_lab_results",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:trauma_lab_results",
        "user_id"   		=> "required:trauma_lab_results",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaLabResult::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}