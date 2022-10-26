<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_bed extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['occupied','bed_name','facility_id','ward_id','bed_type_id','eraser'];
}