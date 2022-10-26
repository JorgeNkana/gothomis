<?php

namespace App\Http\Controllers\Nhif;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\nhif\NhifApproval;
use ServiceManager;
use Auth;
use App\Model\Patient\Tbl_accounts_number;
class NhifPreApprovalController extends Controller
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
        $cardNo                         =     $request->card_no;
        $RefferenceNo                   =     $request->refference_no;
        $ItemCode                       =     $request->item_code;
        $visit_id                       =     $request->visit_id;
         $user_id                        =     Auth::user()->id;
        $manager                        =     new ServiceManager();

        $is_visit_closed=Tbl_accounts_number::where('card_no',$cardNo)
                         ->where('visit_close',1)->get();  

                         if(count($is_visit_closed) ==0){
                          return response()->json([
                            "Message"=>"You can't perform this action , No active visit for this card number",
                            "status"=>500
                          ]);
                         }
        $authorization_number =  $is_visit_closed[0]->authorization_number;
      
           //$result=$manager->AuthorizeCard($cardNo);//Current
          $result=$manager->preApprovalServices($cardNo,$RefferenceNo,$ItemCode);//New implementation of the API that include new parameters
          $result= json_decode($result,true);
           $StatusCode  =$result['StatusCode'];
           $Status      =$result['Status'];

           if($Status =="INVALID"){
            return response()->json([
                'Message' => 'Invalid Refference number ('.$RefferenceNo.')',
                'status' => 500
         ]);
          }
           
            if($StatusCode==200){
               if(NhifApproval::where('refference_number',$RefferenceNo)
                            ->where('card_number',$cardNo)->count()==0){
                NhifApproval::create(['card_number'=>$cardNo ,
                                                    'refference_number'=>$RefferenceNo,
                                                    'item_code'=>$ItemCode,
                                                    'authorization_number'=>$authorization_number,
                                                    'user_id'=>$user_id
                            ]);
                    }
  
            }
           
   
            return response()->json([
                       'Message' => 'Succesfully approved and  added to system',
                       'status' => 200
                    ]);
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