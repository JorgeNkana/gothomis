<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_child_birth_history extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['antenatal','natal','post_natal','nutrition','growth','development','birth_history_id'];
}