<?php

namespace App\GePG;

use Illuminate\Database\Eloquent\Model;

class Tbl_cash_deposit extends Model
{
    protected $fillable = ['transaction','amount','AmountPaid','PspReceiptNumber','user_id','facility_id','BillId','cancelled','paid_at', 'GfsCode', 'drf'];
}