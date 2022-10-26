<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_family_planning_previous_health extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','headache','yellow_fever','heart_disease',
        'bp','diabet','breast_bunje','varicose_vein','kifafa_medics','tb_medics','other_problems' ];

 
}