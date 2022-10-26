<?php

namespace App\Pediatric;

use Illuminate\Database\Eloquent\Model;

class Tbl_pediatric_pre_natal extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['client_id','facility_id','user_id','preg_book_age','clinic_attendance','prophylaxis','others'];
}