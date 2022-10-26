<?php

namespace App\User;

use Illuminate\Database\Eloquent\Model;

class Tbl_last_login extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['user_id','ip','mac_address'];
}