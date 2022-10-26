<?php

namespace App\Facility;

use Illuminate\Database\Eloquent\Model;

class Tbl_lab_test_live extends Model
{
    ////use \App\UuidForKey; 
	protected $fillable = ['facility_id','days','description'];
}