<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaAssesmentPlan extends Model
{
    //
    protected $table = "trauma_assesment_plans";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:trauma_assesment_plans",
        "user_id"   		=> "required:trauma_assesment_plans",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:trauma_assesment_plans",
        "user_id"   		=> "required:trauma_assesment_plans",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaAssesmentPlan::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}