<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaFluidMedication extends Model
{
    //
    protected $table = "trauma_fluid_medications";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:trauma_fluid_medications",
        "user_id"   		=> "required:trauma_fluid_medications",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:trauma_fluid_medications",
        "user_id"   		=> "required:trauma_fluid_medications",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaFluidMedication::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}