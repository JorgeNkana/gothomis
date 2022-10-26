<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_order extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['priority','clinical_note','receiver_id','processor_id','time_received','test_id','order_id','sample_no','eraser','visit_date_id'];
}