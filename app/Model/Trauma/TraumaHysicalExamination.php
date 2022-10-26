<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaHysicalExamination extends Model
{
    //
    protected $table = "trauma_hysical_examinations";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:trauma_hysical_examinations",
        "user_id"   		=> "required:trauma_hysical_examinations",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:trauma_hysical_examinations",
        "user_id"   		=> "required:trauma_hysical_examinations",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaHysicalExamination::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }

}