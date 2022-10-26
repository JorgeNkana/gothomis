<?php

namespace App\laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_result extends Model
{
	//use \App\UuidForKey; 
    //
protected  $fillable=['attached_image','item_id','cancel_reason','panel','post_time','description','order_id', 'visit_date_id','post_user','verified_by','verified_time','approved_by','approved_time'];

}