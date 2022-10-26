<?php

namespace App\Drf;

use Illuminate\Database\Eloquent\Model;

class Tbl_payment extends Model
{
    //
    protected $fillable=[
        'invoice_number',
        'cost_amount',
        'payment_status',
        'payer_name',
        'payslip',
        'payment_agent_name',
    ];
}