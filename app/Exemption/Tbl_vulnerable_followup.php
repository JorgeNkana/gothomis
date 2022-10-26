<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_vulnerable_followup extends Model
{
    //
    protected $fillable=["patient_id","remarks","vulnerable","followup","neglect","facility_id","user_id"];
}