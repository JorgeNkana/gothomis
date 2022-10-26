<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaPastMedicalAllergyHistory extends Model
{
    //
    protected $table = "trauma_past_medical_allergy_histories";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:trauma_past_medical_allergy_histories",
        "user_id"   		=> "required:trauma_past_medical_allergy_histories",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:trauma_past_medical_allergy_histories",
        "user_id"   		=> "required:trauma_past_medical_allergy_histories",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaPastMedicalAllergyHistory::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}