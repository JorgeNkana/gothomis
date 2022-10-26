<?php

namespace App\Results;

use Illuminate\Database\Eloquent\Model;

class Tbl_result extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['test_id','eraser','description','order_id','attached_image','confirmation_status','post_time','notification','post_user','remarks','verify_time','item_id'];
}