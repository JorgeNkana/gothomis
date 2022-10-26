<?php

namespace App\Transactions;

use Illuminate\Database\Eloquent\Model;

class Tbl_depositing extends Model
{
    //
    protected $fillable=[
        'visit_id','patient_id','amount','withdraw','balance','control_in','user_id','facility_id',
        'control'
    ];
}