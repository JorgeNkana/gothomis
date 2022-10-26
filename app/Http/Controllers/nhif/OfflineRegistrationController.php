<?php

namespace App\Http\Controllers\Nhif;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
class OfflineRegistrationController extends Controller
{

    public function __construct(){
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
        
          }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $end_date=$request->end_date;
        $start_date=$request->start_date;
        $sql="SELECT t1.* ,t2.patient_id,t3.facility_code,t2.account_number,t2.id AS account_id,card_no,authorization_number,membership_number,DATE(t2.created_at) AS attended_date,t3.facility_name,t3.address,
          TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) AS age ,t4.practioner_no,t4.name  AS created_by,t2.created_at AS time_created,t2.updated_at AS last_modified,t7.prof_name                
         FROM tbl_patients t1
         INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id
         INNER JOIN users t4 ON t4.id=t2.user_id
         INNER JOIN tbl_facilities t3 ON t3.id=t2.facility_id   
         INNER JOIN tbl_proffesionals t7 ON t4.proffesionals_id=t7.id                  
         WHERE   t2.created_at BETWEEN '".$start_date."' AND  '".$end_date."' 
          AND t2.authorization_number IS  NULL AND t2.card_no IS NOT NULL
        GROUP BY card_no LIMIT 35";
       return DB::SELECT($sql); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}