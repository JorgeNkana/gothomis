<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_exemption extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','patient_id','exemption_reason','exemption_no','exemption_type_id','status_id','reason_for_revoke','status'];
}