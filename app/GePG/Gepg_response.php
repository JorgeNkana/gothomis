<?php

namespace App\GePG;

use Illuminate\Database\Eloquent\Model;

class Gepg_response extends Model
{
    protected $fillable = ['BillId','gepg_response','TrxStsCode'];
}