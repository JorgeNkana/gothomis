<?php

namespace App\Facility;

use Illuminate\Database\Eloquent\Model;

class Tbl_reattendance_free_day extends Model
{
    ////use \App\UuidForKey; 
	protected $fillable = ['facility_id','days','description', 'user_id'];
}