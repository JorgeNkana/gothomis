<?php

namespace App\BloodBank;

use Illuminate\Database\Eloquent\Model;

class Tbl_donor_investigation extends Model
{
    //
    protected $fillable=['facility_id','user_id','patient_id','hb','weight',
        'pr','postpone_reason','evaluation','polygamy','wives'];
}