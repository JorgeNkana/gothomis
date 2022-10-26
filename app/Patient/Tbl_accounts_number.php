<?php

namespace App\Patient;

use Illuminate\Database\Eloquent\Model;

class Tbl_accounts_number extends Model
{
    //use \App\UuidForKey; 
protected  $fillable=['card_no','user_id','patient_id','status','authorization_number','membership_number','tallied','account_number','facility_id','date_attended','scheme_id','visit_type','is_submitted','visit_close'];
}