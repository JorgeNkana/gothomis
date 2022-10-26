<?php

namespace App\GePG;

use Illuminate\Database\Eloquent\Model;

class Gepg_reconciliation_request extends Model
{	
	protected $fillable = ['SpReconcReqId','FacilityCode','ReconcilliationDate'];
}