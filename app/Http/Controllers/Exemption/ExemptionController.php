<?php

namespace App\Http\Controllers\Exemption;


use App\classes\patientRegistration;
use App\ClinicalServices\Tbl_bills_category;
use App\Clinics\Tbl_clinic_instruction;
use App\Exemption\Tbl_client_violence;
use App\Exemption\Tbl_client_violence_informant;
use App\Exemption\Tbl_client_violence_output;
use App\Exemption\Tbl_client_violence_service;
use App\Exemption\Tbl_clients_complain;
use App\Exemption\Tbl_exemption;
use App\Exemption\Tbl_attachment;
use App\classes\SystemTracking;
use App\Exemption\Tbl_vulnerable_followup;
use App\Payment_types\Tbl_pay_cat_sub_category;
use App\Trackable;
use App\Exemption\Tbl_ukatili;
use App\Exemption\Tbl_exemption_access;
use App\Exemption\Tbl_exemption_tracking_status;
use App\Exemption\Tbl_gbv_vac;
use App\Exemption\Tbl_marriage_issue;
use App\Exemption\Tbl_social_issue;
use App\Exemption\Tbl_social_referral;
use App\Exemption\Tbl_social_ward_round;
use App\Exemption\Tbl_violence_output;
use App\Exemption\Tbl_violence_service;
use App\Exemption\Tbl_violence_sub_category;
use App\Item_setups\Tbl_item_type_mapped;
use App\Patient\Tbl_encounter_invoice;
use App\Payments\Tbl_invoice_line;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Patient\Tbl_accounts_number;
use App\Patient\Tbl_patient;

use App\Patient\Tbl_exemption_number;
use DB;

use Illuminate\Support\Facades\Input;

class ExemptionController extends Controller
{
    //

    public function patientsFullData(Request $request)
    {
        $searchKey=$request['searckKey'];
        return DB::table('tbl_residences');

    }

    public function getAllPatient(Request $request)
    {
      $name = $request['name'];
        $patients="SELECT t1.*,t1.id as patient_id,residence_name FROM `tbl_patients`  t1 join tbl_residences t2 on t1.residence_id=t2.id  WHERE t1.search_field LIKE '%".preg_replace("/\s+/","",$name)."%' LIMIT 20";
        return DB::SELECT($patients);

    }
    public function getexemption_services($facility){
        $getPricedItems=DB::table('vw_registrar_services')

            ->where('facility_id',$facility)
            ->where('patient_category_id',1)
 ->groupBy("item_name")
            ->get();
        return $getPricedItems;

    }


    public function patient_exemption(Request $request)

    {
        //return $request->all();
        $facility_id=$request['facility_id'];
        $patient_id=$request['patient_id'];
        $user_id=$request['user_id'];
        $bill_id=$request['bill_id'];
        $exemption_reason=$request['exemption_reason'];
        $exemption_type_id=$request['exemption_type_id'];
        $main_category_id=$request['main_category_id'];
        $payment_filter=$request['payment_filter'];
        $price_id=$request['item_price_id'];
        $quantity=$request['quantity'];
        $status_id=$request['status_id'];
        $item_type_id=$request['item_type_id'];
        $dept_id=$request['dept_id'];
        $change=$request['change'];
        $item_id=$request['item_id'];
        /// echo Input::hasFile($file);
        if(patientRegistration::duplicate('tbl_exemptions',['user_id','patient_id','exemption_reason','exemption_type_id',
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$user_id,$patient_id,$exemption_reason,$exemption_type_id])==true){
            return response()->json([
                'msg'=>'Oops!.. Duplication or Double entry detected.. System detected that, you are entering a
                    Same data set more than once....',
                'status'=>0
            ]);

        }
        else{

//   uploading


            $exemption=Tbl_exemption::where('patient_id',$patient_id)
                ->where('exemption_type_id',$exemption_type_id)->take(1)->orderBy('id','desc')->get();
            if($exemption_reason==""){
                return response()->json([
                    'msg'=>'Please Enter Reason(s) for Exemption ',
                    'status'=>0
                ]);
            }
            if($price_id=="" && $change=='false'){
                return response()->json([
                    'msg'=>'Please Choose service ',
                    'status'=>0
                ]);
            }
            if($change=='true'){
                //$category_number=Tbl_bills_category::where('patient_id',$patient_id)->orderBy('id','desc')->first();
                $category_number=Tbl_bills_category::where('patient_id',$patient_id)->orderBy('created_at','desc')->take(1)->get();
                //return $update_id=$category_number->id;
                if(count($category_number)<1){
                    return response()->json([
                        'msg'=>'Patient Has No Category Yet.... ',
                        'status'=>0
                    ]);
                }
                else{
                    $update_id=$category_number[0]->account_id;
                    $data=Tbl_bills_category::where('account_id',$update_id)->update([
                        'main_category_id'=>$main_category_id,
                        'bill_id'=>$bill_id,

                    ]);
                    $oldData=Tbl_bills_category::where('id',$update_id)->get();
                    $patient_id=$patient_id;
                    $trackable_id=$update_id;
                    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$category_number,$oldData);


                }



            }
            else{



                if($request->input('main_category_id')!=1)
                {
                    $status_id=1;
                    $payment_filter=$request->input('payment_filter');
                }

//
                $account=patientRegistration::patientAccountNumber($facility_id,$patient_id,$user_id);

                $account_number_id=$account;
                $bill_id=$request->input('bill_id');
                $main_category_id=$request->input('main_category_id');


                if(patientRegistration::duplicate('tbl_invoice_lines',array('patient_id','item_type_id','quantity',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($patient_id,$item_type_id,$quantity,''))==true){

                    return response()->json([
                        'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                        'status' => '0'
                    ]);
                }

                else{
					//ADDED TO COUNT ATTENDANCE
					$facility_code=DB::SELECT("SELECT facility_code FROM tbl_facilities t1 WHERE t1.id='".$facility_id."'");
					$facility_code=$facility_code[0]->facility_code;
					$patient=Tbl_patient::where('id',$patient_id)->get();
					$gender= $patient[0]->gender;
					$dob= $patient[0]->dob;
					//check if reattendance
					$reattendance = false;
					if(Tbl_accounts_number::where("patient_id",$patient_id)->count() > 0)
						$reattendance = true;
					if ($reattendance){
						patientRegistration::countReattendance($gender, $dob,$facility_code);
					}else{
						patientRegistration::countNewAttendance($gender, $dob,$facility_code);
					}
					//end attendance
					
                    $payment_category =Tbl_bills_category::create(['patient_id'=>$patient_id,'account_id'=>$account_number_id,'user_id'=>$user_id,'bill_id'=>$bill_id,'main_category_id'=>$main_category_id]);



                    $encounter =Tbl_encounter_invoice::create(array('account_number_id'=>$account_number_id,'facility_id'=>$facility_id,'user_id'=>$user_id));

//return $encounter->id;

                    $invoice_line =Tbl_invoice_line::create(array('invoice_id'=>$encounter->id,'payment_filter'=>$payment_filter,
                        'item_type_id'=>$item_type_id,'facility_id'=>$facility_id,'quantity'=>number_format($quantity, 2, '.', ''),'user_id'=>$user_id,'item_price_id'=>$price_id,'status_id'=>1,'discount_by'=>$user_id,'patient_id'=>$patient_id));


                    $oldData=null;
                    $patient_id=$patient_id;
                    $trackable_id=$invoice_line->id;
                    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$invoice_line,$oldData);
                    //sending to clinic


                    if($request['dept_id']==""){
                        $dept_id=1;
                    }
                    $data= Tbl_clinic_instruction::create([
                            'visit_id'=>$account_number_id,
                            'dept_id'=>$dept_id,
                            'sender_clinic_id'=>24,
                            'received'=>0,
                            'priority'=>'Routine',
                            'specialist_id'=>null,
                            'doctor_requesting_id'=>$user_id,
                            'consultation_id'=>$item_id,
                        ]
                    );
                    $oldData=null;
                    $patient_id=$patient_id;
                    $trackable_id=$data->id;
                    SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);

                }
                // }

            }

            if(count($exemption)==1){
                $exemption_type_id_exists=$exemption[0]->exemption_type_id;
            }
            else{
                $exemption_type_id_exists="";
            }



            $patientExistwithPreviousExemptNumber=Tbl_exemption_number::where('patient_id',$patient_id)
                ->orderBy('id','desc')
                ->take(1)->get();
            //checking if patient has already given exemption number use the existing number
            if(count($patientExistwithPreviousExemptNumber)>0){
                $exemption_no_number=  $patientExistwithPreviousExemptNumber[0]->exemption_number;
                $patient = new Tbl_exemption($request->all());

                $patient['exemption_no'] = $exemption_no_number;
                $patient->save();
                $file =0;

                while (Input::hasFile($file)) {
                    $destinationPath = 'uploads'; // upload path
                    $fileName =  '-'.date('dmyhis').'-'.rand(11111,99999).'.pdf'; // renameing image

                    if(Input::file($file)->move($destinationPath, $fileName)){
                        $admin = new Tbl_attachment($request->all());
                        //return $admin;
                        $admin['file_path']=$fileName;
                        $admin['patient_id']=$patient_id;
                        $admin['describtion']=$exemption_reason;
                        $saved= $admin->save();
                        //return $admin;
                        // return response($admin, 101);


                        if(!$saved){

                            return response()->json([
                                'msg' => 'Error Encounted: Failed to save ',101,
                                'status' => 0
                            ]);

                        }
                        //return response("FILE WAS SUCCESSFULLY UPLOADED.", 200);
                        return response()->json([
                            'msg' => 'EXEMPTION NUMBER ' . ' ' . $exemption_no_number,
                            'status' => 1
                        ]);
                    } else{

                        return response("UNABLE TO UPLOAD FILE", 101);

                    }// uploading file to given path
                    $file++;


                }


                return response()->json([
                    'msg' => 'EXEMPTION NUMBER ' . ' ' . $exemption_no_number,
                    'status' => 1
                ]);


            }

            //checking if patient has not given exemption number create a ne number
            else{
                $exemption_no_number= patientRegistration::patientExemptionNumber($facility_id,$patient_id,$user_id) ;
                $patient = new Tbl_exemption($request->all());
                $patient['exemption_no'] = $exemption_no_number;
                $patient->save();
                $oldData=null;
                $patient_id=$patient_id;
                $trackable_id=$patient->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$patient,$oldData);


                $file =0;
                while (Input::hasFile($file)) {
                    $destinationPath = 'uploads'; // upload path
                    $fileName =  '-'.date('dmyhis').'-'.rand(11111,99999).'.pdf'; // renameing image
                    if(Input::file($file)->move($destinationPath, $fileName)){
                        $admin = new Tbl_attachment($request->all());
                        //return $admin;
                        $admin['file_path']=$fileName;
                        $admin['patient_id']=$patient_id;
                        $admin['describtion']=$exemption_reason;

                        //return $admin;
                        // return response($admin, 101);


                        if(!$admin->save()){

                            return response()->json([
                                'msg' => 'Error Encounted: Failed to save ',101,
                                'status' => 0
                            ]);

                        }
                        //return response("FILE WAS SUCCESSFULLY UPLOADED.", 200);
                        return response()->json([
                            'msg' => 'EXEMPTION NUMBER ' . ' ' . $exemption_no_number,
                            'status' => 1
                        ]);
                    } else{

                        return response("UNABLE TO UPLOAD FILE", 101);

                    }// uploading file to given path
                    $file++;

                }
                return response()->json([
                    'msg' => 'EXEMPTION NUMBER ' . ' ' . $exemption_no_number,
                    'status' => 1
                ]);



            }

        }
    }

    public function exemption_list(Request $request)
    {
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];



        return DB::select(DB::raw("SELECT users.name,tbl_exemptions.*,tbl_patients.dob,tbl_patients.gender,tbl_patients.medical_record_number,
               tbl_patients.first_name,tbl_patients.middle_name,tbl_patients.last_name,tbl_pay_cat_sub_categories.sub_category_name,tbl_pay_cat_sub_categories.pay_cat_id,tbl_pay_cat_sub_categories.id as sub_act_id
                FROM (tbl_exemptions  JOIN tbl_pay_cat_sub_categories ON tbl_exemptions.exemption_type_id = tbl_pay_cat_sub_categories.id
	INNER JOIN tbl_patients ON tbl_exemptions.patient_id = tbl_patients.id 
	INNER JOIN users ON tbl_exemptions.user_id = users.id)
	  
	
               WHERE tbl_exemptions.status !=1 AND users.facility_id='{$facility_id}' AND (tbl_exemptions.created_at BETWEEN '{$start_date}' AND '{$end_date}')"));


    }

    public function temporary_exemption_clients($patient_id)
    {

        return $exemption_list=DB::table('tbl_invoice_lines')
            ->join('tbl_item_prices','tbl_invoice_lines.item_price_id','=','tbl_item_prices.id')
            ->join('tbl_item_type_mappeds','tbl_invoice_lines.item_type_id','=','tbl_item_type_mappeds.id')
            ->join('tbl_items','tbl_item_type_mappeds.item_id','=','tbl_items.id')
            ->where('tbl_invoice_lines.patient_id',$patient_id)
            ->where('tbl_invoice_lines.status_id',1)
            ->where('tbl_invoice_lines.is_payable',true)
            ->where('tbl_invoice_lines.payment_filter',3)
            ->select('tbl_invoice_lines.*','tbl_item_prices.price','tbl_items.item_name')
            ->get();
    }

    public function GetDebts_list_summary(Request $request)
    {

        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];
        return $exemption_list_summary=DB::table('tbl_invoice_lines')
            ->join('tbl_item_prices','tbl_invoice_lines.item_price_id','=','tbl_item_prices.id')
            ->join('tbl_item_type_mappeds','tbl_invoice_lines.item_type_id','=','tbl_item_type_mappeds.id')
            ->join('tbl_items','tbl_item_type_mappeds.item_id','=','tbl_items.id')
            ->where('tbl_invoice_lines.facility_id',$facility_id)
            ->whereBetween('tbl_invoice_lines.created_at',[$start_date,$end_date])
            ->where('tbl_invoice_lines.status_id',1)
            ->where('tbl_invoice_lines.is_payable',true)
            ->where('tbl_invoice_lines.payment_filter',3)
            ->select('tbl_invoice_lines.*','tbl_item_prices.price','tbl_items.item_name')
            ->get();
    }

    public function temporary_exemption_list($facility_id)
    {

        return $exemption_list=DB::table('tbl_invoice_lines')
            ->join('tbl_patients','tbl_invoice_lines.patient_id','=','tbl_patients.id')
            ->join('users','tbl_invoice_lines.user_id','=','users.id')
            ->where('tbl_invoice_lines.facility_id',$facility_id)
            ->where('tbl_invoice_lines.status_id',1)
           ->where('tbl_invoice_lines.is_payable',true)
            ->where('tbl_invoice_lines.payment_filter',3)
            ->select('tbl_invoice_lines.*','tbl_patients.medical_record_number','tbl_patients.first_name','tbl_patients.last_name','tbl_patients.middle_name','tbl_patients.gender','users.name')
            ->groupBy( 'tbl_patients.id')
            ->get();

    }


        public function temporary_exemption_status_update(Request $request)
    {
        $category=Tbl_pay_cat_sub_category::where("pay_cat_id",1)->take(1)->orderBy('id','asc' )->get();
        
        $oldDataa=Tbl_invoice_line::where('patient_id', $request->all()[0]['patient_id'])
            ->where('id', $request->all()[0]['id'])
            ->get();
             $subCatNAme=Tbl_pay_cat_sub_category::where("sub_category_name",$oldDataa[0]->sub_category_name) ->first();
        $payment_category =Tbl_bills_category::where('patient_id',$oldDataa[0]->patient_id)->orderBy('id','desc')->take(1)->get();

        $account_number_id=$payment_category[0]->account_id;
        $encounter =Tbl_encounter_invoice::create(array('account_number_id'=>$account_number_id,'facility_id'=>$oldDataa[0]->facility_id,'user_id'=>$oldDataa[0]->user_id));

        $payment_filter=$subCatNAme->id;
        foreach ($request->all() as $exempt_change) {
 $oldData=Tbl_invoice_line::where('patient_id', $exempt_change['patient_id'])
                ->where('id',$exempt_change['id'])
                ->get();
            $invoice_line =Tbl_invoice_line::create(array('invoice_id'=>$encounter->id,'payment_filter'=>$payment_filter,
                'item_type_id'=>$oldData[0]->item_type_id,'facility_id'=>$oldData[0]->facility_id,'quantity'=>number_format($oldData[0]->quantity, 2, '.', ''),'user_id'=>$oldData[0]->user_id,'item_price_id'=>$oldData[0]->item_price_id,'status_id'=>1,'discount'=>0,'discount_by'=>$oldData[0]->user_id,'patient_id'=>$oldData[0]->patient_id));

            $data1 = Tbl_invoice_line::where('patient_id', $exempt_change['patient_id'])
                ->where('id', $exempt_change['id'])
                ->update([
                    'status_id' => 3 ]);
            $newData=Tbl_invoice_line::where('patient_id', $exempt_change['patient_id'])
                ->where('id', $exempt_change['id'])
                ->get();
            $patient_id=$exempt_change['patient_id'];
            $trackable_id=$newData[0]->id;
            $user_id=$newData[0]->user_id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$newData,$oldData);
        }
//
        return response()->json([
            'msg' => 'ITEMS HAS SUCCESSFUL CONVERTED TO NORMAL PAYMENT',
            'status' => 1
        ]);
    
}






    public function Create_debt(Request $request)
    {

        foreach ($request->all() as $trans_id) {

			$oldData=Tbl_invoice_line::where('id',$trans_id['id'])
                ->where('patient_id',$trans_id['patient_id'])->get();

            $data1 = Tbl_invoice_line::where('id',$trans_id['id'])
                ->where('patient_id',$trans_id['patient_id'])
                ->update([
                    'payment_filter' => 3,

                ]);
			
			DB::statement("delete from bills where id=".$trans_id['id']);
			$patient_id=$trans_id['patient_id'];
			$trackable_id=$trans_id['id'];
			$user_id=$trans_id['user_i'];
			SystemTracking::Tracking($user_id,$patient_id,$trackable_id,Tbl_invoice_line::find($trans_id['id']),$oldData);
        }
//
        return response()->json([
            'msg' => 'ITEM(s) HAS SUCCESSFUL CONVERTED TO DEBT',
            'status' => 1
        ]);
    }



    public function exemption_list_by_gender(Request $request)
    {
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];

        return DB::table('users')
            ->  join('tbl_exemptions','tbl_exemptions.user_id','=','users.id')
            ->  join('tbl_patients','tbl_exemptions.patient_id','=','tbl_patients.id')
            ->join('tbl_pay_cat_sub_categories','tbl_exemptions.exemption_type_id','=','tbl_pay_cat_sub_categories.id')
            ->where('users.facility_id',$facility_id)
            ->where('tbl_exemptions.status','!=1')
            ->select('tbl_patients.gender','sub_category_name as exemption_name',DB::raw('count(tbl_patients.gender) as total') )
            ->whereBetween('tbl_exemptions.created_at',[$start_date,$end_date])
            ->groupBy('sub_category_name','tbl_patients.gender')
            ->orderBy(DB::raw('count(tbl_exemptions.id)'),'desc')
            ->get()  ;
    }
    public function Attachment(Request $request)
    {
        $attachment=$request['attachment'];

        return DB::table('tbl_attachments')
//            ->join('tbl_exemptions','tbl_exemptions.patient_id','=','tbl_attachments.patient_id')
//            ->join('tbl_patients','tbl_attachments.patient_id','=','tbl_patients.id')
            ->where('tbl_attachments.patient_id',$attachment)
            ->select('tbl_attachments.*')
            ->get()  ;
    }

    public function complain_view(Request $request)
    {
        $dept=$request['dept'];
        return
            DB::table('tbl_departments')
                ->  join('tbl_clients_complains','tbl_departments.id','=','tbl_clients_complains.complain_area_id')
                ->where('complain_area_id',$dept)
                ->get()  ;
    }

    public function ward_round(Request $request)
    {
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];

        return DB::table('tbl_social_issues')
            ->  join('tbl_social_ward_rounds','tbl_social_ward_rounds.issue_id','=','tbl_social_issues.id')
            ->where('tbl_social_ward_rounds.facility_id',$facility_id)
            ->whereBetween('tbl_social_ward_rounds.created_at',[$start_date,$end_date])

            ->get()  ;
    }

    public function Update_ward_round_content(Request $request)
    {
        $id=$request['id'];
        Tbl_social_ward_round::where('id',$id)->update($request->all());
        return response()->json([
            'msg' => 'Successful saved ',
            'status' => 1
        ]);
    }
    public function Update_complain_content(Request $request)
    {
        $id=$request['id'];
        Tbl_clients_complain::where('id',$id)->update($request->all());
        return response()->json([
            'msg' => 'Successful saved ',
            'status' => 1
        ]);
    }

    public function patient_exemption_status_update(Request $request)
    {
        $request->all();
        $id=$request['id'];
        $patient_id=$request['patient_id'];
        $main_category_id=$request['main_category_id'];
        $bill_id=$request['bill_id'];
        $sub_act_id=$request['sub_act_id'];
        $main_category_prev=$request['main_category_prev'];
        $sub_category_name=$request['sub_category_name'];
        $sub_category_name1=$request['sub_category_name1'];

        if($sub_act_id == $bill_id){

            return response()->json([
                'msg' => 'You can not Change '.$sub_category_name1.' to '.$sub_category_name,
                'status' => 0
            ]);
        }

        if($main_category_prev != $main_category_id){
//exemption has changed to normal payment and status =1 means this exemption is no longer exist but record for this exempt.. still kept
            $data=Tbl_exemption::where('id',$id)->update([
                'status'=>1,
                'exemption_type_id'=>$sub_act_id,
            ]);
        }
        if($main_category_prev == $main_category_id){

            $data=Tbl_exemption::where('id',$id)->update([

                'exemption_type_id'=>$bill_id,
            ]);
        }
        $category_number=Tbl_bills_category::where('patient_id',$patient_id)->orderBy('id','desc')->take(1)->get();
        $update_id=$category_number[0]->id;
        $data=Tbl_bills_category::where('id',$update_id)->update([
            'main_category_id'=>$main_category_id,
            'bill_id'=>$bill_id,

        ]);

        return response()->json([
            'msg' => 'You have successful Changed '.$sub_category_name1.' to '.$sub_category_name,
            'status' => 1
        ]);

    }

//gbv/vac

    public function violation_registration(Request $request)
    {
        // return$request->all();
        $user_id=$request['user_id'];
        $patient_id=$request['patient_id'];
        $description=$request['describtion'];
        $date=Date('Y-m-d');


        $file=0;



        while (Input::hasFile($file)) {
            $destinationPath = 'uploads'; // upload path
            $fileName =  $patient_id.'-'.date('dmyhis').'-'.rand(11111,99999).'.pdf'; // renameing image

            if(Input::file($file)->move($destinationPath, $fileName)){
                $admin = new Tbl_attachment($request->all());
                //return $admin;
                $admin['file_path']=$fileName;
                $admin['patient_id']=$patient_id;
                $admin['describtion']=$description;

                //return $admin;
                // return response($admin, 101);


                if(!$admin->save()){

//                        return response()->json([
//                            'msg' => 'Error Encounted: Failed to save ',101,
//                            'status' => 0
//                        ]);

                }
                //return response("FILE WAS SUCCESSFULLY UPLOADED.", 200);

            } else{

                return response("UNABLE TO UPLOAD FILE", 101);

            }// uploading file to given path
            $file++;


        }

        return response()->json([
            'msg' => 'Successful saved ',
            'status' => 1
        ]);


    }

    public function gbv_vac_list($facility)
    {
        return DB::table('vw_gbv_vacs')->where('facility_id',$facility)
            ->groupBy('violence_type_category')
            ->groupBy('violence_type_name')
            ->groupBy('gender')
            ->select('gender','violence_type_category','violence_type_name',DB::raw('count(gender) as total') )
            ->get();
    }

    public function vulnerables(Request $request)
    {
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];
        return DB::table('vw_vulnerables')
            ->where('facility_id',$facility_id)
            ->whereBetween('date',[$start_date,$end_date])->get()  ;
    }



    public function complain_report(Request $request)
    {
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];
        return DB::table('tbl_departments')
            ->  join('tbl_clients_complains','tbl_clients_complains.complain_area_id','=','tbl_departments.id')
            ->where('facility_id',$facility_id)
            ->select('complain_area_id','department_name',DB::raw('count(tbl_clients_complains.id) as count'))
            ->whereBetween('tbl_clients_complains.created_at',[$start_date,$end_date])
            ->groupBy('department_name','complain_area_id')
            ->orderBy(DB::raw('count(tbl_clients_complains.id)'),'desc')
            ->get()  ;
    }
    public function exemption_filter_by_employee(Request $request)
    {
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];
        return DB::table('users')
            ->  join('tbl_exemptions','tbl_exemptions.user_id','=','users.id')
            ->where('facility_id',$facility_id)
            ->where('tbl_exemptions.status','!=1')
            ->select('name',DB::raw('count(tbl_exemptions.id) as count'))
            ->whereBetween('tbl_exemptions.created_at',[$start_date,$end_date])
            ->groupBy('name')
            ->orderBy(DB::raw('count(tbl_exemptions.id)'),'desc')
            ->get()  ;
    }


    public function violence_sub_registration(Request $items)
    {
        foreach ($items->all() as $request){
            Tbl_violence_sub_category::create([
                'violence_category_id'=>$request['violence_category_id'],
                'sub_violence'=>$request['sub_violence'],
            ]);

        }

        return response()->json([
            'msg' => 'Saved',
            'status' => 1
        ]);
    }
    public function violence_service_registration(Request $items)
    {
        foreach ($items->all() as $request){
            Tbl_violence_service::create([
                'service_name'=>$request['service_name'],
            ]);

        }

        return response()->json([
            'msg' => 'Saved',
            'status' => 1
        ]);
    }

    public function violence_output_registration(Request $items)
    {
        foreach ($items->all() as $request){
            Tbl_violence_output::create([
                'output'=>$request['output'],
            ]);

        }

        return response()->json([
            'msg' => 'Saved',
            'status' => 1
        ]);
    }
    public function violence_client_registration(Request $items)
    {

        foreach ($items->all() as $request){
            if(patientRegistration::duplicate('tbl_client_violences',['user_id','patient_id','violence_category_id','violence_type_id',
                    '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                    [$request['user_id'],$request['patient_id'],$request['violence_category_id'],$request['violence_category_id']])==true){
            }
            Tbl_client_violence::create([
                'violence_type_id'=>$request['violence_type_id'],
                'sub_violence_id'=>$request['sub_violence_id'],
                'violence_category_id'=>$request['violence_category_id'],
                'patient_id'=>$request['patient_id'],
                'user_id'=>$request['user_id'],
                'facility_id'=>$request['facility_id'],
                'event_date'=>$request['event_date'],
            ]);

        }

        return response()->json([
            'msg' => 'Saved',
            'status' => 1
        ]);
    }

    public function violence_client_service_registration(Request $items)
    {
        foreach ($items->all() as $request){
            if(patientRegistration::duplicate('tbl_client_violence_services',['user_id','patient_id','service_id',
                    '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                    [$request['user_id'],$request['patient_id'],$request['service_id']])==true){
            }
            Tbl_client_violence_service::create([
                'service_id'=>$request['service_id'],
                'patient_id'=>$request['patient_id'],
                'user_id'=>$request['user_id'],
                'facility_id'=>$request['facility_id'],

            ]);

        }

        return response()->json([
            'msg' => 'Saved',
            'status' => 1
        ]);
    }

    public function violence_client_output_registration(Request $items)
    {
        foreach ($items->all() as $request){
            if(patientRegistration::duplicate('tbl_client_violence_outputs',['user_id','patient_id','output_id',
                    '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                    [$request['user_id'],$request['patient_id'],$request['output_id']])==true){
            }
            Tbl_client_violence_output::create([
                'output_id'=>$request['output_id'],
                'patient_id'=>$request['patient_id'],
                'user_id'=>$request['user_id'],
                'facility_id'=>$request['facility_id'],

            ]);

        }

        return response()->json([
            'msg' => 'Saved',
            'status' => 1
        ]);
    }
    public function marriage_issues_register(Request $request)
    {

        if(patientRegistration::duplicate('tbl_marriage_issues',['user_id','patient_id','complainer_description',
                '((timestampdiff(year,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request['user_id'],$request['patient_id'],$request['complainer_description']])==true){
            return response()->json([
                'msg' => 'Data Duplications Detected',
                'status' => 0
            ]);
        }
        Tbl_marriage_issue::create($request->all());



        return response()->json([
            'msg' => 'Marriage Issues Saved',
            'status' => 1
        ]);
    }
    public function Update_conflict_content(Request $request)
    {


        Tbl_marriage_issue::where('id',$request['id'])->update($request->all());



        return response()->json([
            'msg' => 'Marriage Issues Updated',
            'status' => 1
        ]);
    }
    public function violence_client_informant_registration(Request $request)
    {

        if(patientRegistration::duplicate('tbl_client_violence_informants',['user_id','patient_id','relationship',
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request['user_id'],$request['patient_id'],$request['relationship']])==true){

            return response()->json([
                'msg' => 'Duplication entry',
                'status' => 0
            ]);
        }
        Tbl_client_violence_informant::create([
            'relationship'=>$request['relationship'],
            'patient_id'=>$request['patient_id'],
            'user_id'=>$request['user_id'],
            'facility_id'=>$request['facility_id'],
            'description'=>$request['description']

        ]);



        return response()->json([
            'msg' => 'Saved',
            'status' => 1
        ]);
    }

    public function social_issue_register(Request $request)
    {

        if(patientRegistration::duplicate('tbl_social_issues',['issue_name',
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$request['issue_name']])==true){

            return response()->json([
                'msg' => 'Duplication entry',
                'status' => 0
            ]);
        }
        Tbl_social_issue::create([
            'issue_name'=>$request['issue_name'],
        ]);



        return response()->json([
            'msg' => 'Saved',
            'status' => 1
        ]);

    }
    public function ward_round_register(Request $request)
    {
        if($request['patient_id']==''){
            return response()->json([
                'msg' => 'Choose Patient',
                'status' => 0
            ]);
        }
        if($request['issue_id']==''){
            return response()->json([
                'msg' => 'Choose Issue raised',
                'status' => 0
            ]);
        }
        if($request['plan']==''){
            return response()->json([
                'msg' => 'Fill your Plan',
                'status' => 0
            ]);
        }

        $patient_id=$request['patient_id'];
        $plan=$request['plan'];
        $issue_id=$request['issue_id'];

        if(patientRegistration::duplicate('tbl_social_ward_rounds',['patient_id','issue_id','plan',
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [ $request['patient_id'],$request['issue_id'],$request['plan']])==true){

            return response()->json([
                'msg' => 'Duplication entry',
                'status' => 0
            ]);
        }
        Tbl_social_ward_round::create([
            'patient_id'=>$request['patient_id'],
            'user_id'=>$request['user_id'],
            'facility_id'=>$request['facility_id'],
            'issue_id'=>$request['issue_id'],
            'plan'=>$request['plan'],
            'output'=>$request['output'],
            'remarks'=>$request['remarks'],
        ]);

        return response()->json([
            'msg' => "Client's social round has Saved",
            'status' => 1
        ]);
    }

    public function client_complains_register(Request $request)
    {

        if($request['patient_id']==''){
            return response()->json([
                'msg' => 'Choose Patient',
                'status' => 0
            ]);
        }
        if($request['complain_area_id']==''){
            return response()->json([
                'msg' => 'Choose Area of Complain raised',
                'status' => 0
            ]);
        } if($request['complain']==''){
        return response()->json([
            'msg' => 'Please Fill client Complain',
            'status' => 0
        ]);
    }

        $patient_id=$request['patient_id'];
        $plan=$request['plan'];
        $issue_id=$request['issue_id'];

        if(patientRegistration::duplicate('tbl_clients_complains',['patient_id','complain_area_id','complain',
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$request['complain_area_id'],$request['complain']])==true){

            return response()->json([
                'msg' => 'Duplication entry',
                'status' => 0
            ]);
        }
        Tbl_clients_complain::create($request->all());

        return response()->json([
            'msg' => 'Client Complains Saved',
            'status' => 1
        ]);
    }
    public function social_referral_registry(Request $request)
    {

        if($request['patient_id']==''){
            return response()->json([
                'msg' => 'Choose Patient',
                'status' => 0
            ]);
        }
        if($request['facility_name']=='' && $request['ref_type']=='INN'){
            return response()->json([
                'msg' => 'Enter Facility Name where Referral is Coming from',
                'status' => 0
            ]);
        }if($request['facility_name']=='' && $request['ref_type']=='OUT'){
        return response()->json([
            'msg' => 'Enter Facility Name where Referral is Going',
            'status' => 0
        ]);
    }

        $patient_id=$request['patient_id'];


        if(patientRegistration::duplicate('tbl_social_referrals',['patient_id','facility_name','ref_type',
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$request['facility_name'],$request['ref_type']])==true){

            return response()->json([
                'msg' => 'Duplication entry',
                'status' => 0
            ]);
        }
        Tbl_social_referral::create($request->all());

        return response()->json([
            'msg' => 'Client Referral Details Saved',
            'status' => 1
        ]);
    }

    public function exemption_user_configure(Request $request)
    {

        foreach ($request->all() as $record)   {
            $user_id=$record['user_id'];
            $exempt_id=$record['exempt_id'];
            $data=Tbl_exemption_access::where('user_id',$user_id)
                ->where('exempt_id',$exempt_id)
                ->where('status',1)
                ->get();
            if(count($data)>0){

            }
            else{
                Tbl_exemption_access::create([
                    'exempt_id'=>$exempt_id,
                    'user_id'=>$user_id,
                    'status'=>1,
                ]);
            }

        }
        return response()->json([
            'msg' => 'Access Successful Granted',
            'status' => 1
        ]);
    }
    public function get_violence_sub_category($item)
    {
        return    Tbl_violence_sub_category::where('violence_category_id',$item)->get();
    }
    public function social_issue_list()
    {
        return    Tbl_social_issue::get();
    }

    public function get_violence_output_registration()
    {
        return    Tbl_violence_output::get();
    }

    public function get_violence_service_registration()
    {
        return    Tbl_violence_service::get();
    }

    public function exemption_finance( Request $request)
    {

        $start=$request['start_date'];
        $end=$request['end_date'];
        $facility_id=$request['facility_id'];
       /* return DB::table('vw_exemption_financials')
            ->where('facility_id',$facility_id)
            ->select('vw_exemption_financials.*',DB::raw('(price*quantity) as total'))
            ->whereBetween('vw_exemption_financials.created_at',[$start,$end])
            //->groupBy('dept_id')
            //->groupBy('pay_cat_id')
            ->get();*/
       return DB::select("SELECT  (price*quantity) as total,
                    t4.item_name, 
                    t10.department_name, 
                    
                    FROM tbl_invoice_lines t1
INNER JOIN tbl_item_prices t3 ON t1.item_price_id = t3.id
INNER JOIN tbl_items t4 ON t3.item_id = t4.id  
INNER join tbl_pay_cat_sub_categories t7 on t1.payment_filter = t7.id
 INNER join tbl_departments t10 on t10.id=t4.dept_id
  INNER join tbl_patients t9 on t1.patient_id=t9.id
 where t7.pay_cat_id=3  AND created_at BETWEEN '".$start."' AND   '".$end."'   group by t10.id");

    }

    public function exemption_finance_detail( Request $request)
    {

        $start=$request['start_date'];
        $end=$request['end_date'];
        $facility_id=$request['facility_id'];
        return DB::table('vw_exemption_service_summary')
            ->where('facility_id',$facility_id)
            ->whereBetween('vw_exemption_service_summary.created_at',[$start,$end])

            ->get();
    }

    public function marriage_issues_list( Request $request)
    {

        $start=$request['start_date'];
        $end=$request['end_date'];
        $facility_id=$request['facility_id'];
        $all=[];
        $all[]= DB::table('tbl_marriage_issues')
            ->join('tbl_patients','tbl_marriage_issues.patient_id','=','tbl_patients.id')
            ->join('users','tbl_marriage_issues.user_id','=','users.id')
            ->where('tbl_marriage_issues.facility_id',$facility_id)
            ->select('tbl_marriage_issues.*','users.name','tbl_patients.first_name','tbl_patients.middle_name','tbl_patients.last_name','tbl_patients.gender','tbl_patients.dob','tbl_patients.medical_record_number')
            ->whereBetween('tbl_marriage_issues.created_at',[$start,$end])
            ->get();
        $all[]=DB::select("SELECT
   IFNULL(count(CASE when status =1  then 1 ELSE  0 END ),0) as Complete,
   IFNULL(count(CASE when status =0  then 1 ELSE  0 END ),0) as Incomplete,
   IFNULL(count(CASE when status =2  then 1 ELSE  0 END ),0) as Incourt
   from   tbl_marriage_issues   
   
 WHERE facility_id='$facility_id'  and created_at BETWEEN '".$start."' and '".$end."' group by status
   ");
        return $all;
    }

    public function exemption_finance_depts( Request $request)
    {

        $start=$request['start_date'];
        $end=$request['end_date'];
        $facility_id=$request['facility_id'];
        return DB::table('vw_exemption_service_summary')
            ->where('facility_id',$facility_id)
            ->select('vw_exemption_service_summary.*',DB::raw('sum(price*quantity-discount) as total'),DB::raw('count(*) as idadi'))
            ->whereBetween('vw_exemption_service_summary.created_at',[$start,$end])
            ->groupBy('dept_id')
            ->get();

       // return DB::select("select department_name as idara, sum(price*quantity-discount) as total,count(*) as idadi FROM vw_exemption_service_summary WHERE created_at like '%2018-09%' GROUP by dept_id");

    }
    public function exemption_sub_dept_finance( Request $request)
    {
          
        $facility_id = $request->input('facility_id');
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $sql = "SELECT SUM(`resultant_pay`) AS total,`sub_department_name`,COUNT(id) AS transactions,sub_category_name FROM `vw_exemp_sub_department_summary` WHERE created_at BETWEEN '".$start."'  AND '".$end."' AND `facility_id`=".$facility_id." GROUP BY `sub_department_name`,sub_category_name";
        $dptReport = DB::select(DB::raw($sql));
        return $dptReport;

    }

    public function patients_address_info($patient_id)
    {

        $d=DB::table('tbl_patients')
            ->join('tbl_countries','tbl_countries.id','=','tbl_patients.country_id')
            ->join('tbl_occupations','tbl_occupations.id','=','tbl_patients.occupation_id')
            ->join('tbl_residences','tbl_residences.id','=','tbl_patients.residence_id')
            ->select('mobile_number','residence_name','occupation_name','country_name as nationality','dob','tbl_patients.created_at')
            ->where('tbl_patients.id',$patient_id)
            ->get();
        return $d;

    }

    public function vulnerable_followup_neglect(Request $request)
    {


      $patient_id=$request->all()["patient_id"];
        $vulnerable=$request->all()["vulnerable"];
        $followup=$request->all() ["followup"];
        $neglect=$request->all() ["neglect"];
        $user_id=$request->all() ["user_id"];

        if(patientRegistration::duplicate('tbl_vulnerable_followups',["patient_id","vulnerable","followup","neglect",
                '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$patient_id,$vulnerable,$followup,$neglect])==true){

            return response()->json([
                'msg' => 'Duplication entry',
                'status' => 0
            ]);
        }
        $data= Tbl_vulnerable_followup::create($request->all());
        $trackable_id=$data->id;
        $oldData="";
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$data);

        return response()->json([
            'msg' => 'Successfull saved',
            'status' => 1
        ]);
    }

      public function violances(Request $request)
    {

        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
        $facility_id=$request['facility_id'];
        $all=[];
        $sql1_all_attended="SELECT 
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE' then 1 ELSE  0 END ),0) as male_total_attendance
   from     tbl_ukatilis   
 WHERE   created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($sql1_all_attended);

        $sql1_new_attended="SELECT distinct
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE' then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE' then 1 ELSE  0 END ),0) as male_total_attendance
   from     tbl_ukatilis   
 WHERE    followup IS NULL OR followup ='NO' and  created_at BETWEEN '".$start_date."' and '".$end_date."' ";
        $all[] = DB::select($sql1_new_attended);

        $sql1_re_attended="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  from     tbl_ukatilis   
 WHERE    followup='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'  ";
        $all[] = DB::select($sql1_re_attended);

        $sql1_mazingira_hatarishi="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
    from     tbl_ukatilis   
 WHERE    vulnerable IS NOT NULL and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_mazingira_hatarishi);
        $sql1_maswali_ya_utambuzi_kwa_mteja="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
    from     tbl_ukatilis   
 WHERE    screening='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_maswali_ya_utambuzi_kwa_mteja);
        $sql1_maswali_ya_utambuzi_kwa_niaba="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
    from     tbl_ukatilis   
 WHERE    screening='PARENT' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_maswali_ya_utambuzi_kwa_niaba);

        $sql1_physical_violence="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
   from     tbl_ukatilis   
 WHERE    pv_violence='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_physical_violence);

        $sql1_sexual_violence="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
    from     tbl_ukatilis   
 WHERE    sv_violence='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_sexual_violence);

        $sql1_emossional_violence="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
   from     tbl_ukatilis   
 WHERE    ev_violence='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_emossional_violence);

        $sql1_neglect="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  from     tbl_ukatilis   
 WHERE    ng_violence='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_neglect);

        $sql1_services_given_kisheria="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
   from     tbl_ukatilis   
 WHERE    fi_service='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_services_given_kisheria);

        $sql1_services_given_unasihi="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
  from     tbl_ukatilis   
 WHERE    c_service='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_services_given_unasihi);
        $sql1_services_given_unasihi_kwa_niaba_ya_watoto="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  from     tbl_ukatilis   
 WHERE    c_service='PARENT' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_services_given_unasihi_kwa_niaba_ya_watoto);

        $sql1_services_given_kupima_virusi_vya_ukimwi="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  from     tbl_ukatilis   
 WHERE    hiv_result IS NOT NULL and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_services_given_kupima_virusi_vya_ukimwi);

        $sql1_services_given_matibabu_ya_kinga="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
   from     tbl_ukatilis   
 WHERE    pep_service='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_services_given_matibabu_ya_kinga);
        $sql1_services_given_matibabu_yatokanayo_na_magonjwa_ya_ngono="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  from     tbl_ukatilis   
 WHERE    sti_service='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_services_given_matibabu_yatokanayo_na_magonjwa_ya_ngono);

        $sql1_services_given_NjiayaUzaziwaMpangowaDharura="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
   from     tbl_ukatilis   
 WHERE    fp_service='YES' and  created_at BETWEEN'".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_services_given_NjiayaUzaziwaMpangowaDharura);

        $sql1_services_given_Kipolisi="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
   from     tbl_ukatilis   
 WHERE    p_service='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_services_given_Kipolisi);

        $sql1_violence_output_Ulemavuwakimwiliwakudumu="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
   from     tbl_ukatilis   
 WHERE    disability='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_violence_output_Ulemavuwakimwiliwakudumu);

        $sql1_violence_ndaniyamasaa72baadayatukio="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
 from tbl_ukatilis   
 WHERE    within_72_hrs='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_violence_ndaniyamasaa72baadayatukio);

        $sql1_violence_ndaniyamasaa72baadayatukionakukutwahawanaujauzito="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance  from   tbl_ukatilis   
 WHERE    within_72_hrs='YES' and  created_at BETWEEN    '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_violence_ndaniyamasaa72baadayatukionakukutwahawanaujauzito);

        $sql1_referal_rufaakujakituoni="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
   
   from   tbl_ukatilis   
 WHERE     incoming_referral='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";
        $all[] = DB::select($sql1_referal_rufaakujakituoni);

        $sql1_referal_rufaandaniyakituo="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
    
   from   tbl_ukatilis   
 WHERE     internal_referral='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $all[] = DB::select($sql1_referal_rufaandaniyakituo);

        $sql1_referal_rufaakwendanjeyakituo="SELECT
   IFNULL(sum(CASE when gender ='FEMALE'     AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as female_0_59_month,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0) as female_5_9_year,
  IFNULL(sum(CASE when gender ='FEMALE'    AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as female_10_14_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as female_15_17_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as female_18_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as female_above_24_year,
  IFNULL(sum(CASE when gender ='FEMALE'  then 1 ELSE  0 END ),0) as female_total_attendance,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(MONTH ,dob,'".$end_date."') <50  then 1 ELSE  0 END ),0) as male_0_59_month,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 5 and 9  then 1 ELSE  0 END ),0)as male_5_9_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 10 and 14  then 1 ELSE  0 END ),0) as male_10_14_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 15 and 17  then 1 ELSE  0 END ),0) as male_15_17_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') BETWEEN 18 and 24  then 1 ELSE  0 END ),0) as male_18_24_year,
  IFNULL(sum(CASE when gender ='MALE'   AND timestampdiff(YEAR ,dob,'".$end_date."') >24  then 1 ELSE  0 END ),0) as male_above_24_year,
  IFNULL(sum(CASE when gender ='MALE'  then 1 ELSE  0 END ),0) as male_total_attendance
  
   from   tbl_ukatilis   
 WHERE    outgoing_referral='YES' and  created_at BETWEEN '".$start_date."' and '".$end_date."'   ";

        $all[] = DB::select($sql1_referal_rufaakwendanjeyakituo);

        return $all;

    }

    public function SocialWelfareData(Request $request)
    {
         Tbl_ukatili::create($request->all());
         return response()->json([
             "msg"=>"Record successful saved..",
             "status"=>1
         ]);
    }
    public function UpdateSocialWelfareData(Request $request)
    {
        $id=$request->all()['id'];
         Tbl_ukatili::where('id',$id)->update($request->all());
         return response()->json([
             "msg"=>"Record successful updated..",
             "status"=>1
         ]);
    }

    public function SocialWelfareDataHistorory(Request $request)

    {

 
       return Tbl_ukatili::where('patient_id',$request->all()['patient_id'])->orderBy("id","desc")->get();
    }




}