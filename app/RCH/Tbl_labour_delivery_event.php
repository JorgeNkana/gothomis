<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_labour_delivery_event extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','delivery_date','vitamin_given','place_of_delivery',
        'number_of_newborn','method_of_delivery','reason_for_scisoring','placenter_removed','placenter_removed_date',
    'blood_discharged','labour_catalyst','msamba','tailer_id','bp','comment','midwife_name'];
}