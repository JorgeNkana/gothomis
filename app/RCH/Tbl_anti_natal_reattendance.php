<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_anti_natal_reattendance extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','weigth','hb','bp','urine_albumin','urine_sugar',
       'followup_date' ,'pregnancy_age','pregnancy_height','baby_position','baby_pointer','baby_play','baby_heart_beat','oedema','twins'];
}