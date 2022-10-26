<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_bills_category extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable = ['patient_id','user_id','account_id','bill_id','main_category_id'];
}