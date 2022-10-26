<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_physical_examination_record extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['other_systems_summary','observation','category','system','physical_examination_id','local_examination','gen_examination','summary_examination'];
}