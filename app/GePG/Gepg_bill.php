<?php

namespace App\GePG;

use Illuminate\Database\Eloquent\Model;

class Gepg_bill extends Model
{
    protected $fillable = ['InvoiceId','CashDeposit','BillId','FacilityCode', 'BillAmount','PyrName', 'PyrCellNum', 'PyrEmail', 'BillGenBy', 'BillGenDt', 'BillExprDt'];
}