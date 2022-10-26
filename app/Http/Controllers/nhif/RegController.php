<?php

namespace App\Http\Controllers\Nhif;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ClaimSubmission;
//use App\Model\nhif\Tbl_nhif_file;
use Auth;
use ServiceManager;
use App\Model\Nhif\AccountNumber;
use DB;

//use App\Model\nhif\Tbl_bulk_claim;
class RegController extends Controller
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
        $response=[];
        $AuthorizationNo=null;
        $MembershipNo =null;
        $manager=new ServiceManager();
        $result="";      
        $cardNo =     $request->card_number;
        $VisitTypeID= $request->visit_type;
        $facility_id= Auth::user()->facility_id;
        $ReferralNo= $request->referal_no; 
       // $account_id =     $request->account_id;

         //$result=$manager->AuthorizeCard($cardNo);//Current
        $result=$manager->AuthorizeCard($cardNo,$VisitTypeID,$ReferralNo,$facility_id);//New implementation of the API that include new parameters
        $result= json_decode($result,true);
        if(isset($result['AuthorizationNo'])){
            $AuthorizationNo  =$result['AuthorizationNo'];
        }
        
        if(isset($result['MembershipNo'])){
            $MembershipNo     =$result['MembershipNo'];
        }
        $response[0]=$result;
        $response[1]=$VisitTypeID;

       // AccountNumber::where('id',$account_id)->update(['authorization_number'=>$AuthorizationNo,
                                                         //     'membership_number'=>$MembershipNo]);

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       


       
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
    public function update(Request $request)
    {
        //
        return $request;
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