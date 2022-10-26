<?php

namespace App\TB;

use Illuminate\Database\Eloquent\Model;

class Tbl_tb_sputam_test_followup extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','facility_id','client_id','month'];
}