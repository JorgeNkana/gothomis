<?php

namespace App\Laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_tb_leprosy_request extends Model
{
   protected $fillable=[
        "dtlc_email",
  "dtlc_name",
 "hiv_status",
 "patient_id",
 "pre_tb_treatment",
 "reason_for_examination",
 "rtlc_email",
 "rtlc_name",
  "specimen_type",
 "test_requested",
"user_id",
 "visit_id",
       "month_on_treatment",
       "status",
   ];
}