<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_body_system extends Model
{
	//use \App\UuidForKey; 
protected $fillable = ['name','category'];
}