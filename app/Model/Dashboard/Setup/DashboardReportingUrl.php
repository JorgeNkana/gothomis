<?php

namespace App\Model\Dashboard\Setup;

use Illuminate\Database\Eloquent\Model;

class DashboardReportingUrl extends Model
{
    //use SoftDeletes;

    protected $table       =  "tbl_st_dashboard_reporting_urls";
    protected $guarded     =  ['id'];
	
    protected $softDelete = true;

    public static $create_rules = [
	
    ];

    public static $rules = [
        
    ];
}