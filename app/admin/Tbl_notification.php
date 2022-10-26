<?php

namespace App\admin;

use Illuminate\Database\Eloquent\Model;

class Tbl_notification extends Model
{
	//use \App\UuidForKey; 
     protected  $fillable=['receiver_id','sender_id','message','delete_sms'];
}