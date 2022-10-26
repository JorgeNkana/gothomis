<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trackable extends Model {

    protected $table    =  'trackables';
    protected $guarded  =  ['id'];
    protected $fillable =  array('user_id','patient_id', 'trackable_id', 'action', 'trackable_type','new_value','old_value');

    public function trackable() {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
}