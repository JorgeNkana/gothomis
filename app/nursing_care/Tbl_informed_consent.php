<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_informed_consent extends Model
{
	//use \App\UuidForKey; 
    //
    protected  $fillable=['visit_date_id','item_id','relationshipsID','admission_id','patient_id','relative_name','dateSigned','facility_id','user_id'];

}