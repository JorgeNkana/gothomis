<?php

namespace App\Model\Patient;

use Illuminate\Database\Eloquent\Model;

class Tbl_patient_registration_report extends Model
{
    //use \App\UuidForKey; 
	 protected  $fillable=['facility_id',
	                       'date',
	 					   'male_under_one_month',
	 					   'female_under_one_month',
	 					   'total_under_one_month',
	 					   'male_under_one_year',
	 					   'female_under_one_year',
	 					   'total_under_one_year',
	 					   'male_under_five_year',
	 					   'female_under_five_year',
	 					   'total_under_five_year',
	 					   'male_above_five_under_sixty',
	 					   'female_above_five_under_sixty',
	 					   'total_above_five_under_sixty',
	 					   'male_above_sixty',
	 					   'female_above_sixty',
	 					   'total_above_sixty',
	 					   'total_male',
	 					   'total_female',
	 					   'grand_total'
	 					];
	
	 
}