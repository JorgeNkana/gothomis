<?php

namespace App\Vital;

use Illuminate\Database\Eloquent\Model;

class Tbl_vital extends Model
{
	 protected  $fillable=['vital_sign','vital_sign_value','visiting_id','registered_by'];
}