<?php

namespace App\Model\Trauma;

use Illuminate\Database\Eloquent\Model;

class TraumaChiefComplaint extends Model
{
    //

    protected $table = "tbl_trauma_chief_complaints";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');

    public static $rules = [
        "client_id"   	=> "required:tbl_trauma_chief_complaints",
        "complaint"  	=> "required:tbl_trauma_chief_complaints",
        "user_id"   		=> "required:tbl_trauma_chief_complaints",

    ];

    public static $create_rules = [
        "client_id"   	=> "required:tbl_trauma_chief_complaints",
        "complaint"  	=> "required:tbl_trauma_chief_complaints",
        "user_id"   		=> "required:tbl_trauma_chief_complaints",

    ];

    public static function isDuplicate($new, $id = 0){
        $records = TraumaChiefComplaint::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));

        return $records->count() > 0 ? True : False;
    }
}