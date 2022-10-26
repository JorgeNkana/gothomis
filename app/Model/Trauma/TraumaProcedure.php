<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaProcedure extends Model
{
    //
    protected $table = "trauma_procedures";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:trauma_procedures",
        "user_id"   		=> "required:trauma_procedures",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:trauma_procedures",
        "user_id"   		=> "required:trauma_procedures",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaProcedure::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}