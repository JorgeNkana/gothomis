<?php

namespace App\BloodBank;

use Illuminate\Database\Eloquent\Model;

class Tbl_blood_donation extends Model
{
    //
    protected $fillable=['facility_id','user_id','patient_id','syring_injected_time',
'syring_removed_time','vein_success','little_blood','donation_success','bad_event'];
}