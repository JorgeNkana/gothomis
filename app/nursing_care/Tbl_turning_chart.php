<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_turning_chart extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['facility_id',
        'visit_date_id',
        'admission_id',
        'admission_id',
        'date_recorded',
        'time_recorded',
        'position',
        'remarks',
        'user_id'

    ];
}