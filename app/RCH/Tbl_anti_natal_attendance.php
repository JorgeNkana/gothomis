<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_anti_natal_attendance extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','weight','hb','bp','urine_albumin','urine_sugar',
        'followup_date','date_attended' ,'pregnancy_height','baby_position','baby_pointer','baby_play','baby_heart_beat','oedema','twins'];
}