<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaClientDiagnosis extends Model
{
    //
    protected $table = "trauma_client_diagnoses";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:trauma_client_diagnoses",
        "user_id"   		=> "required:trauma_client_diagnoses",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:trauma_client_diagnoses",
        "user_id"   		=> "required:trauma_client_diagnoses",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaClientDiagnosis::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}