<?php

namespace App\Environmental;

use Illuminate\Database\Eloquent\Model;

class Tbl_anti_rabies_vaccination extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','facility_id','ant_rabies_name','quantity','status','batch_no',];
}