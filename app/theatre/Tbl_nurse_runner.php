<?php

namespace App\theatre;

use Illuminate\Database\Eloquent\Model;

class Tbl_nurse_runner extends Model
{
    protected $fillable = [
        'item_id', 'material_id', 'user_id',
        'visit_id', 'given', 'used',
        'start_time', 'end_time',
        'drainage', 'tourniquet', 'implants',
        'implant_screws', 'pathology_specimen', 'comment'
    ];
}