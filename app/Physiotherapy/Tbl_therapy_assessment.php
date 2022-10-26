<?php

namespace App\Physiotherapy;

use Illuminate\Database\Eloquent\Model;

class Tbl_therapy_assessment extends Model
{
	//use \App\UuidForKey; 
    protected $fillable =['patient_id','visit_date_id','user_id','facility_id','general',
        'specific','neurological','summary'];
}