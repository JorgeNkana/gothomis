<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class Trauma_accident_location extends Model
{
    //

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:trauma_accident_locations",
        "user_id"   		=> "required:trauma_accident_locations",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:trauma_accident_locations",
        "user_id"   		=> "required:trauma_accident_locations",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = Trauma_accident_location::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}