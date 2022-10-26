<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_labour_delivery_complication extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','vaginal_bleeding','prom','preeclampsia','eclampsia','anaemia',
    'sepsis','malaria','hiv_p','pph','fgm','obstructed_labour','three_tear','retained_placenta','chest_pain','loss_strength','other_complication'];
}