<?php

namespace App\Inventory;

use Illuminate\Database\Eloquent\Model;

class Tbl_inventory_receiving extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['batch','quantity','cost_price','description','asset_number','serial_number','item_id',
    'supplier','control_balance','order_number','order_status','receive_type'];

    public function issuings(){
        return $this->hasMany('App\Tbl_inventory_issuing');
    }
}