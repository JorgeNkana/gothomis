<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_permission extends Model
{
	
    protected  $fillable=['module','glyphicons','title','main_menu','keyGenerated'];
}