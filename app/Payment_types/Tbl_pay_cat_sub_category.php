<?php

namespace App\Payment_types;

use Illuminate\Database\Eloquent\Model;

class Tbl_pay_cat_sub_category extends Model
{
	
    //
    protected $fillable=['pay_cat_id','facility_id','sub_category_name'];
}