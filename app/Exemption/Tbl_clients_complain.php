<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_clients_complain extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','patient_id','facility_id','complain_area_id','complain','immediate_measure'
        ,'solution','remarks'];
}