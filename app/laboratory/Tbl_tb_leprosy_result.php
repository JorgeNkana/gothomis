<?php

namespace App\Laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_tb_leprosy_result extends Model
{
    //
    protected $fillable=[

        "comment",
       "ear_lobe",
       "laboratory_serial_no",
       "lesion",
       "reception_date",
       "result",
       "specimen",
       "zn_fm",
       "user_id",
       "visit_id",
        "patient_id",
       "appearance",
        "request_id",
        "verified_by",
        "status"
    ];
}