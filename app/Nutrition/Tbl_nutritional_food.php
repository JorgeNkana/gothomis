<?php

namespace App\Nutrition;

use Illuminate\Database\Eloquent\Model;

class Tbl_nutritional_food extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','user_id','facility_id','visit_id','suppliment_id','description'];
}