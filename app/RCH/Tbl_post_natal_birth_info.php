<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_post_natal_birth_info extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','number_of_delivery','delivery_date','place_of_delivery','midwife_proffesion',
  'mother_status','number_of_newborn','number_of_newborn_alive','number_of_newborn_died'];


}