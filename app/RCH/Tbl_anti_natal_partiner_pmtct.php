<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_anti_natal_partiner_pmtct extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','vvu_infection','has_counsel_given','counselling_date',
        'has_taken_vvu_test','date_of_test_taken','vvu_first_test_result','counselling_after_vvu_test',
        'vvu_second_test_result'];
}