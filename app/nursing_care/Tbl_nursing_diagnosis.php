<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_nursing_diagnosis extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['diagnosis_name'];
}