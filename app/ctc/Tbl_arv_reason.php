<?php

namespace App\ctc;

use Illuminate\Database\Eloquent\Model;

class Tbl_arv_reason extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['code','arv_reason'];
}