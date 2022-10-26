<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_post_natal_child_arv_prophlaxise extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','arv_id','time'];
}