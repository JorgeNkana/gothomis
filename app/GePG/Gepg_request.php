<?php

namespace App\GePG;

use Illuminate\Database\Eloquent\Model;


class Gepg_request extends Model
{
    protected $fillable = ["facility_code","BillId", "BillAmt", "BillExprDt","PayerId","PyrName","BillGenDt","BillGenBy","BillApprBy","PyrCellNum","PyrEmail","PyrId","date","BillDesc"];
}