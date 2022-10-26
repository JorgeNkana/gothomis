<?php

namespace App\Model\nhif;

use Illuminate\Database\Eloquent\Model;

class Tbl_nhif_file extends Model
{
	//use \App\UuidForKey; 
    protected $fillable =['facility_id','user_id','claims','account_id'];
}