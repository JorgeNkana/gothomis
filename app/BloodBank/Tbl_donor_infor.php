<?php

namespace App\BloodBank;

use Illuminate\Database\Eloquent\Model;

class Tbl_donor_infor extends Model
{
    //
    protected $fillable=['donor_condition','donor_type','facility_id','user_id','patient_id','post_address','phy_address','fax','donor_no','last_donation_date','last_donation_place'];
}