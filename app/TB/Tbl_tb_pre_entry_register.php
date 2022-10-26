<?php

namespace App\TB;

use Illuminate\Database\Eloquent\Model;

class Tbl_tb_pre_entry_register extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','facility_id','client_id','referral_type','client_type'];
}