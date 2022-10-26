<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_treatment_chart extends Model
{
	//use \App\UuidForKey; 
     protected  $fillable=['admission_id','type_of_drugs_dosage_id','how_often'];
}