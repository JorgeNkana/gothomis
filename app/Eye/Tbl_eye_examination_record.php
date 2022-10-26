<?php

namespace App\Eye;

use Illuminate\Database\Eloquent\Model;
use Emadadly\LaravelUuid\Uuids;

class Tbl_eye_examination_record extends Model
{
	//use \App\UuidForKey; 
    protected $fillable= ['description','sub_category','category','clinic_visit_id','non_perception_light',
        'perception_light','hand_movement','sphere','cylinder','axis','v_a','p_d','a_d_d'];
}