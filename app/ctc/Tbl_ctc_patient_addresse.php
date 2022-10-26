<?php

namespace App\ctc;

use Illuminate\Database\Eloquent\Model;

class Tbl_ctc_patient_addresse extends Model
{
    protected  $fillable=['name_ten_cell_leader','name_head_house_hold','contact_house_hold_head','on_off','residence_id','patient_id','user_id'];
}