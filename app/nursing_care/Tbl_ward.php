<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_ward extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['ward_class_id','ward_name','ward_type_id','facility_id'];
}