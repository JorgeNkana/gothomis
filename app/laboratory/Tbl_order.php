<?php

namespace App\laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_order extends Model
{
	//use \App\UuidForKey; 
    //
protected  $fillable=['sample_types','order_id','priority',
'clinical_note','receiver_id','eraser','processor_id','result_control','time_received','order_validator_id','test_id','order_control','order_status','order_cancel_reason',
'sample_no','facility_id', 'visit_date_id'];
}