<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_family_social_history extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['chronic_illness','substance_abuse','adoption','others','family_history_id'];
}