<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_input extends Model
{
	//use \App\UuidForKey; 
  protected  $fillable=['facility_id',
						'visit_date_id',
						'admission_id',
						'admission_id',					 
						'date_recorded',
						'time_recorded',
						'type_iv',
						'amount_iv',
						'type_oral',
						'amount_oral',
						'user_id'
  
  ];
}