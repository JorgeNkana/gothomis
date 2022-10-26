<?php

namespace App\Environmental;

use Illuminate\Database\Eloquent\Model;

class Tbl_nuisance_compose extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['nuisance_id','cause','location','abatement','user_id','facility_id','event_date'];
}