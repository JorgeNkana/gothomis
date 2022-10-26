<?php

namespace App\chats;

use Illuminate\Database\Eloquent\Model;

class tbl_notification extends Model
{
    protected  $fillable=['delete_sms','receiver_id','sender_id','message'];

}