<?php

namespace App\Emergency;

use Illuminate\Database\Eloquent\Model;

class Tbl_emergency_survey_history extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['appearance','airway','breathing','circulation',
        'disability','exposure','intervention','survey_history_id'
    ];
}