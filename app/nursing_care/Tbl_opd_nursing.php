<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_opd_nursing extends Model
{
    protected $fillable=['facility_id','service_type','item_id','visit_id','patient_id','user_id','status','periodic','duration','route'];
}