<?php

namespace App\patient;

use Illuminate\Database\Eloquent\Model;

class Tbl_encounter_invoice extends Model
{
	//use \App\UuidForKey; 
      protected $fillable=[
        'account_number_id','user_id','corpse_id','facility_id'
    ];
}