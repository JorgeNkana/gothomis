<?php

namespace App\admin;

use Illuminate\Database\Eloquent\Model;

class Tbl_permission extends Model
{
	 
    protected  $fillable=['main_menu','module','glyphicons' ,'title','keyGenerated'];
}