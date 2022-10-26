<?php

namespace App\Emergency;

use Illuminate\Database\Eloquent\Model;

class Tbl_emergency_patient extends Model
{
	//use \App\UuidForKey;  
        protected $fillable=['visiting_id','emergency_type_id','registered_by'];

}