<?php

namespace App\Http\Controllers\Nhif;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class VerifiedClaimController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try {
            $start_time= $request->start_time;
            $end_time= $request->end_time;     
            $sql="SELECT t1.* ,t2.patient_id,t3.facility_code,t2.account_number,t2.id AS account_id,card_no,authorization_number,membership_number,DATE(t2.created_at) AS attended_date,t3.facility_name,t3.address,t6.occupation_name,
              TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) AS age ,t4.practioner_no,t4.name  AS created_by,t2.created_at AS time_created,t2.updated_at AS last_modified,t7.prof_name,(SELECT name FROM users t10 WHERE
              t10.id=t9.user_id  LIMIT 1)  AS verified_by,t9.created_at AS time_verified              
             FROM tbl_patients t1
             INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id
             LEFT JOIN tbl_bills_categories t5 ON t2.id=t5.account_id
             INNER JOIN users t4 ON t4.id=t2.user_id
             INNER JOIN tbl_facilities t3 ON t3.id=t2.facility_id   
             LEFT JOIN tbl_occupations t6 ON t6.id=t1.occupation_id 
             INNER JOIN tbl_proffesionals t7 ON t4.proffesionals_id=t7.id   
             INNER JOIN tbl_bulk_claims t9 ON t9.account_id=t5.account_id             
             WHERE  t2.claim_submitted=0
             AND   t2.created_at BETWEEN '".$start_time."' AND  '".$end_time."' 
             AND t2.authorization_number IS NOT NULL
             GROUP BY card_no LIMIT 35";
            
           return DB::SELECT($sql); 
           } catch (\Exception $e) {
             return response()->json([
                                   "Message"=>"Claim was not saved, ".$e,
                                   "status"=>"error"
                                 ]);
           }
           
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