<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_post_natal_breast_status extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','breast_rupture','mastitis','abscess','breast_contriction'];
}