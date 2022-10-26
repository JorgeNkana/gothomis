<?php

namespace App\VCT;

use Illuminate\Database\Eloquent\Model;

class Tbl_vct_register extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['client_id','user_id','facility_id','client_from',
    'referral_to','serial_no','attendance_type','pregnancy_record','client_from_other','referral_to_other',
    'counselling_type','agreed_vvu_test','counselling_after_test','vvu_test_result','participatory_test_result',
    'tb_test','tb_test_result','condom_given','comment'];
}