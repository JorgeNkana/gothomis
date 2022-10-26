<?php

namespace App\Paediatric;

use Illuminate\Database\Eloquent\Model;

class Tbl_dtc_attendance extends Model
{
    //

    protected $fillable=[
        "male_less_moth",
        "female_less_moth",
        "total_less_moth",
        "male_moth_less_year",
        "female_moth_less_year",
        "total_moth_less_year",
        "male_year_five_year",
        "female_year_five_year",
        "total_year_five_year",
        "total",
        "facility_code",
        "reporting_date",
        "book_row_number",
        "attendance_date",
        "coverage_plan_code"
    ];
}