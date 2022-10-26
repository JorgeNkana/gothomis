<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_nursing_care extends Model
{
	//use \App\UuidForKey; 
   protected  $fillable=['admission_id','date_planned','time_planned','diagnosis_name','objective','implementation','evaluation','facility_id','user_id'];

}