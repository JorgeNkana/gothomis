<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_death_condition extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['facility_id',
        'visit_date_id',
        'admission_id',
        'admission_id',
        'description',
        'user_id'

    ];
}