<?php

namespace App\GePG;

use Illuminate\Database\Eloquent\Model;

class Gepg_account extends Model
{
    protected $fillable = ['url','SpCode','SubSpCode','SpSysId','Ccy','RemFlag','BillPayOpt', 'PaymentMethod','GfsCode', 'FacilityCode', 'live'];
}