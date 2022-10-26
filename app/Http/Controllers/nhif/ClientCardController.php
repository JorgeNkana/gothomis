<?php

namespace App\Http\Controllers\Nhif;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Nhif\AccountNumber;
use Auth;
use DB;
class ClientCardController extends Controller
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
        $sql="SELECT t1.* ,t2.account_number,t2.id AS account_id,card_no,authorization_number,membership_number,t2.created_at AS attended_time,
           t4.name AS registered_by
         FROM tbl_patients t1
         INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id
         INNER JOIN users t4 ON t4.id=t1.user_id
           WHERE t2.card_no IS NOT NULL AND visit_close=1
        GROUP BY card_no LIMIT 100";
       return DB::SELECT($sql); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id= Auth::user()->id;
        $account_id= $request->account_id;
        try{
           $giveCard= AccountNumber::where('id',$account_id)->update(['closed_by'=>$user_id,'visit_close'=>0]);
      
         if($giveCard){
           return response()->json([
                'Message' => 'Card was successfully given ',
                'status' => 'success'
            ]);
         }
         return response()->json([
            'Message' => 'Client not found in database',
            'status' => 'error'
        ]);

        }catch(\Exception $e){
    
          return response()->json([
                'Message' => 'Faled to Process, with error logs '.$e,
                'status' => 'error'
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