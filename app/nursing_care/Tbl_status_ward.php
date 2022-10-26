<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_status_ward extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['facility_id',
                          'visit_date_id',
                          'admission_id',
                          'admission_status_id',
                          'ward_id',
                          'user_id'

    ];
}