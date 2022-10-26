<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaClientDisposition extends Model
{
    //
    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:trauma_client_dispositions",
        "user_id"   		=> "required:trauma_client_dispositions",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:trauma_client_dispositions",
        "user_id"   		=> "required:trauma_client_dispositions",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaClientDisposition::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}