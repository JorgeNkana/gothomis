<?php

namespace App\Http\Controllers\nursing_care;

use App\classes\patientRegistration;
use App\classes\SystemTracking;
use App\Item_setups\Tbl_item;
use App\nursing_care\Tbl_opd_nursing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OpdNursingController extends Controller
{
    //

    public function opd_nurse_service(Request $request)
    {
        $searchKey=$request['searchKey'];
        return DB::table('tbl_items')
            ->Where('item_name','like','%'.$searchKey.'%')
            ->get();
    }
    public function checkServicePaymentStatus(Request $request)
    {
      $visit_id=$request['visit_id'];
      $main_category_id=$request['main_category_id'];
        $item_id=$request['item_id'];
        return DB::table('tbl_item_type_mappeds')
            ->join('tbl_invoice_lines','tbl_item_type_mappeds.id','=','tbl_invoice_lines.item_type_id')
            ->join('tbl_item_prices','tbl_item_prices.id','=','tbl_invoice_lines.item_price_id')
            ->join('tbl_bills_categories','tbl_bills_categories.bill_id','=','tbl_invoice_lines.payment_filter')
            ->Where('tbl_item_type_mappeds.item_id','=',$item_id)
            ->Where('tbl_bills_categories.account_id','=',$visit_id)
            ->Where('tbl_bills_categories.main_category_id','=',$main_category_id)
            ->groupBy('tbl_item_type_mappeds.item_id')
           // ->Where('tbl_invoice_lines.status_id','=',2)
           // ->orWhere('tbl_bills_categories.main_category_id','>',1)
            ->get();
    }
    public function SaveOpdService(Request $request)
    {
        $item_id=$request['item_id'];
        $user_id=$request['user_id'];
        $patient_id=$request['patient_id'];
        $service_type=$request['service_type'];
        $facility_id=$request['facility_id'];
        $visit_id=$request['visit_id'];
        $periodic=$request['periodic'];
        $duration=$request['duration'];
        $start=$request['start'];
        if(patientRegistration::duplicate('tbl_opd_nursings',['user_id','patient_id','service_type','item_id','visit_id', '((timestampdiff(minute,created_at,CURRENT_TIMESTAMP)<=3))'],
                [$user_id,$patient_id,$service_type,$item_id,$visit_id])==true){
            return response()->json([
                'msg'=>'Duplication Detected.... Please Try again after 3 Minutes',
                'status'=>0
            ]);
        }
        if($start ==false){
          $checkerDurtion=DB::table('tbl_opd_nursings')
                ->select('periodic',DB::raw('periodic-count(tbl_opd_nursings.item_id) as remain'))
                ->Where('tbl_opd_nursings.visit_id','=',$visit_id)
                ->Where('tbl_opd_nursings.item_id','=',$item_id)->get();
if($checkerDurtion[0]->remain>=1){
    $update=Tbl_opd_nursing::where('item_id','=',$item_id)->where('visit_id','=',$visit_id)
        ->where('status','=',0)->update([
            'status'=>1
        ]);
    if($checkerDurtion[0]->remain>1){
        $data=Tbl_opd_nursing::create($request->all());
        $oldData=null;
        $trackable_id=$data->id;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);

        return response()->json([
            'msg'=>'Item Successful Received',
            'status'=>1
        ]);

    }
    else{
        $data=Tbl_opd_nursing::create([
            'user_id'=>$user_id,
            'facility_id'=>$facility_id,
            'patient_id'=>$patient_id,
            'status'=>1,
            'item_id'=>$item_id,
            'visit_id'=>$visit_id,
            'periodic'=>$periodic,
            'periodic'=>$periodic,
            'duration'=>$duration,
            'service_type'=>$service_type,
        ]);
        $oldData=null;
        $trackable_id=$data->id;
        SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);

        return response()->json([
            'msg'=>'Item Successful Received',
            'status'=>1
        ]);

    }
}
            else{
                return response()->json([
                    'msg'=>'Oops!!!, You have Reached a maximum number of Dosage Duration!',
                    'status'=>0
                ]);

            }

        }

        else{
            $data=Tbl_opd_nursing::create($request->all());
            $oldData=null;
            $trackable_id=$data->id;
            SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$data,$oldData);

            return response()->json([
                'msg'=>'Item Successful Received',
                'status'=>1
            ]);
        }

    }

    public function getOnGoingDosage($facility)
    {
        return DB::table('tbl_patients')
            ->join('tbl_opd_nursings','tbl_opd_nursings.patient_id','=','tbl_patients.id')
            ->Where('tbl_opd_nursings.facility_id','=',$facility)
            ->Where('tbl_opd_nursings.status','=',0)
            ->groupBy('tbl_opd_nursings.patient_id')
            ->get();
    }
    public function loadPatientDosagePregres($visit_id)
    {
        return DB::table('tbl_patients')
            ->join('tbl_opd_nursings','tbl_opd_nursings.patient_id','=','tbl_patients.id')
            ->join('tbl_items','tbl_opd_nursings.item_id','=','tbl_items.id')
            ->join('users','tbl_opd_nursings.user_id','=','users.id')
->select('users.name','tbl_patients.*','tbl_opd_nursings.*','tbl_items.item_name',DB::raw('periodic-count(tbl_opd_nursings.item_id) as remain'))
            ->Where('tbl_opd_nursings.visit_id','=',$visit_id)
            ->groupBy('tbl_opd_nursings.item_id')
            ->orderBy(DB::raw('periodic-count(tbl_opd_nursings.item_id)'),'desc')
            ->get();
    }
    public function ViewProgressDosage(Request $request)
    {
        $item_id=$request['item_id'];
        $visit_id=$request['visit_id'];
        return DB::table('tbl_patients')
            ->join('tbl_opd_nursings','tbl_opd_nursings.patient_id','=','tbl_patients.id')
            ->join('tbl_items','tbl_opd_nursings.item_id','=','tbl_items.id')
            ->join('users','tbl_opd_nursings.user_id','=','users.id')
            ->select('users.name','tbl_patients.*','tbl_opd_nursings.*','tbl_items.item_name',DB::raw('periodic-count(tbl_opd_nursings.item_id) as remain'))
            ->Where('tbl_opd_nursings.visit_id','=',$visit_id)
            ->Where('tbl_opd_nursings.item_id','=',$item_id)

            ->get();
    }
    public function ViewDosageCompleteness(Request $request)
    {
        $item_id=$request['item_id'];
        $visit_id=$request['visit_id'];
        return DB::table('tbl_patients')
            ->join('tbl_opd_nursings','tbl_opd_nursings.patient_id','=','tbl_patients.id')
            ->join('tbl_items','tbl_opd_nursings.item_id','=','tbl_items.id')
            ->join('users','tbl_opd_nursings.user_id','=','users.id')
            ->select('users.name','tbl_patients.first_name','tbl_patients.middle_name','tbl_patients.last_name',
                'tbl_patients.dob','tbl_patients.id',
                'tbl_patients.gender',
                'tbl_opd_nursings.*','tbl_items.item_name')
            ->Where('tbl_opd_nursings.visit_id','=',$visit_id)
            ->Where('tbl_opd_nursings.item_id','=',$item_id)
            ->get();
    }

    public function opd_nursing_report(Request $request)
    {
      $facility_id=$request->facility_id;
    $start=$request->data['start_date'];
      $end=$request->data['end_date'];
       return DB::table('tbl_opd_nursings')
            ->join('tbl_items','tbl_items.id','=','tbl_opd_nursings.item_id')
            ->select(DB::raw('count(tbl_opd_nursings.item_id) as quantity'),'service_type','item_name')
            ->whereBetween('tbl_opd_nursings.created_at',[$start,$end])
            ->groupBy('item_id')
            ->orderBY(DB::raw('count(tbl_opd_nursings.item_id)'),'desc')

            ->get();

    }

    public function cancel_opd_dosage($patient_id)
    {
        $update=Tbl_opd_nursing::where('patient_id','=',$patient_id)
            ->where('status','=',0)->update([
                'status'=>1
            ]);
        return response()->json([
            'msg'=>'Dosage Successful Cancelled/Stopped ',
            'status'=>1
        ]);
    }
}