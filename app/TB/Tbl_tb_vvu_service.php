<?php

namespace App\TB;

use Illuminate\Database\Eloquent\Model;

class Tbl_tb_vvu_service extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','facility_id','client_id','cpt','cpt_start_date','art_start_date'];
}