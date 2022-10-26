<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_labour_observation extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','labour_start_date','amniotic_bust','amniotic_bust_date','baby_possition',
    'baby_pointer','sacral_promontary_reached','ischial_spine_apeared','narrow_outlet','large_servix','temperature','bp','hb',
    'blood_bleeding','baby_heart_beat','comment'];
}