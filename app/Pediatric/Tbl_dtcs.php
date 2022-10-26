<?php

namespace App\Pediatric;

use Illuminate\Database\Eloquent\Model;

class Tbl_dtcs extends Model
{
    //
    protected $fillable=[
        'patient_id','facility_id','user_id','visit_id','dir_duration',
        'water_sugar_loss','stool_blood','fever','vomiting','other_sign',
        'ors_in','intravesel_water','zink_in','other_treatment',
        'ors_out','zink_out','dct_duration','dtc_unit','output'
    ];
}