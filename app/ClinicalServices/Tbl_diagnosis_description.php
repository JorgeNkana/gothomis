<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_diagnosis_description extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['description','code'];
}