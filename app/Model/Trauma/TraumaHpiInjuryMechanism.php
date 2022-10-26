<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaHpiInjuryMechanism extends Model
{
    //

    protected $table = "trauma_hpi_injury_mechanisms";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:trauma_hpi_injury_mechanisms",
        "user_id"   		=> "required:trauma_hpi_injury_mechanisms",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:trauma_hpi_injury_mechanisms",
        "user_id"   		=> "required:trauma_hpi_injury_mechanisms",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaHpiInjuryMechanism::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}