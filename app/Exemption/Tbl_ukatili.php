<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_ukatili extends Model
{
    //

    protected $fillable=[
            "followup",
            "vulnerable",
            "event_date",
            "screening",
            "within_72_hrs",
            "pt_result",
            "hiv_result",
            "sti_result",
            "disability",
            "referred_to",
             "user_id",
            "patient_id",
            "dob",
            "residence_name",
            "gender",
            "client_name",
             "medical_record_number",
            "mobile_number",
            "facility_id",
         "pv_violence",
        "sv_violence",
        "ev_violence",
        "ng_violence",
        "fi_service",
        "im_service",
        "c_service",
        "pep_service",
        "sti_service",
      "ec_service",
        "fp_service",
        "p_service",
      "la_service",
        "sws_service",
        "incoming_referral",
        "internal_referral",
        "outgoing_referral",
        "dept_name",
        "incoming_from",

    ];
}