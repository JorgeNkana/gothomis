<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_previous_pregnancy_indicator extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','fp_35_years_n_above','lp_10_years_n_above',
    'delivery_method','fbs_msb','miscarriage_three_plus','heart_disease','diabetic','tb','waist_disability',
    'high_bleeding','placenta_stacked'];
}