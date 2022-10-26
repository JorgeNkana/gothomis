<?php

namespace App\Emergency;

use Illuminate\Database\Eloquent\Model;

class Tbl_vital_sign extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['visiting_id','vital_sign_id','vital_sign_value','registered_by',
        'date_taken','time_taken'
    ];
}