<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_obs_gyn_record extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['menarche','menopause','cycle','period','menstrual_cycles','std','due_date','contraceptives',
    'abortions','lnmp','gravidity','parity','living_children','obs_gyn_id','gestational_age','category'];
}