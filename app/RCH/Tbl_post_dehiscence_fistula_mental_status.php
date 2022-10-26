<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_post_dehiscence_fistula_mental_status extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','dehiscence_join','fistula','mental_ability'];
}