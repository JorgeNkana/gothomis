<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_pre_history_anethetic extends Model
{
     //
	 //use \App\UuidForKey; 
  protected  $fillable=['remarks','visit_date_id','item_id','history_type','admission_id','patient_id','medical','surgical','anethetic','descriptions','facility_id','user_id'];

}