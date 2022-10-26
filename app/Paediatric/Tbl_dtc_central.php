<?php

namespace App\Paediatric;

use Illuminate\Database\Eloquent\Model;

class Tbl_dtc_central extends Model
{
    //
    protected $fillable=[
        "attendance_male_less_moth",
			"attendance_female_less_moth",
			"attendance_total_less_moth",
			"attendance_male_moth_less_year",
			"attendance_female_moth_less_year",
			"attendance_total_moth_less_year",
			"attendance_male_year_five_year",
			"attendance_female_year_five_year",
			"attendance_total_year_five_year",
			"attendance_total",
        "facility_code"
    ];
}