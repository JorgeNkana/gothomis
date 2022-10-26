<?php

namespace App\Emergency;

use Illuminate\Database\Eloquent\Model;

class Tbl_comma_scale extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','visit_date_id','user_id','facility_id',
        'admission_id'
    ];
}