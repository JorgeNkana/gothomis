<?php

namespace App\Http\Controllers\Transactions;

use App\classes\patientRegistration;
use App\ClinicalServices\Tbl_bills_category;
use App\Exemption\Tbl_discount_reason;
use App\Transactions\Tbl_depositing;
use App\Transactions\Tbl_invoice_line;
use App\Patient\Tbl_patient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class Invoice_linesController extends Controller
{
    //
    // CRUD

    public function loadDiscountBill($id)
    {
        
      
        $sql="SELECT discount,
age as dob ,
facility_id ,
name as first_name, 
gender ,
id,
id as item_referrence,
receipt_number as invoice_id,
item_name,
patient_id,
price,
quantity  FROM bills where patient_id='".$id."'";
     return   DB::select($sql);
    }




    public function invoice_discount(Request $request)
    {
$request = $request->all();
foreach ($request as $discloop){
    $id=$discloop['id'];
    $discount=$discloop['discount'] ;
    $discount_by=$discloop['user_id'];
    $discountDAta= Tbl_invoice_line::where('id',$id)->update(

        [
            'discount'=> $discount,
            'discount_by'=> $discount_by,
        ]);

}


    }

    public function discountingReason(Request $request)
    {
        $reason=   $request['discount_reason'];
        $patient_id=   $request['patient_id'];
        $receipt_number=   $request['receipt_number'];
        $facility_id=   $request['facility_id'];
         if($reason=="")
         {

         }
        else{
          return Tbl_discount_reason::create([
              'discount_reason'=>$reason,
              'patient_id'=>$patient_id,
              'receipt_number'=>$receipt_number,
              'facility_id'=>$facility_id,
          ]) ;
        }



}
    public function return_change(Request $request)
    {

    $patient_id=$request['patient_id'];
        $visit_id=$request['visit_id'];
        $name=$request['name'];

//         if(patientRegistration::duplicate('tbl_depositings',array('patient_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=1))"), array($patient_id))==true) {
//
//             return response()->json([
//                 'msg' => 'Duplication Detected',
//                 'status' => '0'
//             ]);
//         }
        $deposit=$request['action_type'];
        $user_id=$request['user_id'];
        $facility_id=$request['facility_id'];

        $patientBalance= Tbl_depositing::where('patient_id','=',$patient_id)
            ->take(1)->orderBy('id','desc')->get();

        $balanceCariedForward= $patientBalance[0]->balance;

        //checking if amount deposited is enough or not
        if($balanceCariedForward>0){
            Tbl_depositing::where('id','=',$patientBalance[0]->id)->update([
                'control'=>'c',
                'balance'=>null
            ]);
            Tbl_depositing::create([
                'patient_id'=>$patient_id,
                'visit_id'=>$visit_id,
                'facility_id'=>$facility_id,
                'user_id'=>$user_id,
                'withdraw'=>$balanceCariedForward,
                'balance'=>0,
                'control_in'=>$deposit,
                'control'=>'l',
            ]);


        }
        else{
            return response()->json([
                'msg' => ' No Change remained in <b>'.$name .'</b> deposit account',
                'status' => 0
            ]);
        }



    return response()->json([
        'msg' => '<b>'.$balanceCariedForward.'</b>Tsh(s) Has return to <b>'.$name .'</b> as Change',
        'status' => 1
    ]);





     }
    public function deposit_summary(Request $request)
    {



    $patient_id=$request['patient_id'];
    $dep=Tbl_depositing::where('patient_id','=',$patient_id)
        ->take(1)->orderBy('id','desc')->get();
    if (count($dep)>0){
        $dill= Tbl_bills_category::where('patient_id','=',$patient_id)->take(1)->orderBy('id','desc')->get();
        $visit_id=$dill[0]->account_id;


//        $visit_id=$request['visit_id'];

        $withdraw=$request['withdraw'];

         if(patientRegistration::duplicate('tbl_depositings',array('patient_id','visit_id','withdraw',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=1))"), array($patient_id,$visit_id,$withdraw))==true) {

             return response()->json([
                 'msg' => 'Deposit Duplication detected, Please wait atleast 2 Minutes',
                 'status' => '0'
             ]);
         }

        $id_in=$dep[0]->id;
        $balance_in=$dep[0]->balance;

        $deposit=$request['action_type'];
        $user_id=$request['user_id'];
        $facility_id=$request['facility_id'];

        $patientBalance= Tbl_depositing::where('patient_id','=',$patient_id)
            ->take(1)->orderBy('id','desc')->get();

        $balanceCariedForward= $patientBalance[0]->balance;

        //checking if amount deposited is enough or not
        if($balanceCariedForward>=$withdraw){
            Tbl_depositing::where('id','=',$patientBalance[0]->id)->update([
                'control'=>'c',
                'balance'=>null
            ]);
            Tbl_depositing::create([
                'patient_id'=>$patient_id,
                'visit_id'=>$visit_id,
                'facility_id'=>$facility_id,
                'user_id'=>$user_id,
                'withdraw'=>$withdraw,
                'balance'=>($balanceCariedForward-$withdraw),
                'control_in'=>$deposit,
                'control'=>'l',
            ]);


        }
        else{
            return response()->json([
                'msg' => ' Insufficient Fund on Your Deposit, only' .'<b> '. $balanceCariedForward.'</b> '. 'out of <b>'. $withdraw.'</b>',
                'status' => 0
            ]);
        }

    }
    else{
        return response()->json([
            'msg' => 'No Deposit Detail for   '. $request['name'],
            'status' => 0
        ]);



}


        return response()->json([
            'msg' => ' Deposit Deduction Successful Done',
            'status' => 1
        ]);
     }





    public function saveDepositCash(Request $request)
    {

        if ($request['amount']==''){
            return response()->json([
                'msg' => 'Fill amount to Deposit',
                'status' => 0
            ]);
        }
        $amount=$request['amount'];
        $deposit=$request['action_type'];
        $patient_id=$request['patient_id'];
        $user_id=$request['user_id'];
        $facility_id=$request['facility_id'];

     $dill= Tbl_bills_category::where('patient_id','=',$patient_id)->take(1)->orderBy('id','desc')->get();

//checking if patient has account number previously
     if (count($dill)==0){
         return response()->json([
             'msg' => 'Patient Has not Visit Account Number Please Go to Receptionist to Start Visit Before Deposit start',
             'status' => 0
             ]);
     }
     else{
         $patientexist= Tbl_depositing::where('patient_id','=',$patient_id)->take(1)->orderBy('id','desc')->get();

         $visit_id=$dill[0]->account_id;

         if(patientRegistration::duplicate('tbl_depositings',array('patient_id','visit_id','amount',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=1))"), array($patient_id,$visit_id,$amount))==true){

             return response()->json([
                 'msg' => 'Deposit Duplication detected, Please wait atleast 2 Minutes',
                 'status' => '0'
             ]);
         }

         //checking if patient in existing in deposit table in order to carry forward his balance if was not returned to him at that visit
         if (count($patientexist)>0){
             //get
             $patientBalance= Tbl_depositing::where('id','=',$patientexist[0]->id)->get();
             Tbl_depositing::where('id','=',$patientBalance[0]->id)->update([
                 'balance'=>null,
'control'=>'c'
             ]);
           $balanceCariedForward= $patientBalance[0]->balance;
             Tbl_depositing::create([
                 'patient_id'=>$patient_id,
                 'visit_id'=>$visit_id,
                 'facility_id'=>$facility_id,
                 'user_id'=>$user_id,
                 'amount'=>$amount,
                 'balance'=>($amount + $balanceCariedForward),
                 'control_in'=>$deposit,
                 'control'=>'l',

             ]);
         }
         else{
             Tbl_depositing::create([
                 'patient_id'=>$patient_id,
                 'visit_id'=>$visit_id,
                 'facility_id'=>$facility_id,
                 'user_id'=>$user_id,
                 'amount'=>$amount,
                 'balance'=>$amount,
                 'control_in'=>$deposit,
                 'control'=>'l',

             ]);
         }


         return response()->json([
             'msg' => ' Deposit Successful Done',
             'status' => 1
         ]);
     }



}
 public function getEmployeeDepositing_lists(Request $request)
    {
        $start=$request['start_date'];
        $end=$request['end_date'];
      $all[]= DB::select("SELECT  t1.*,sum(amount) as amount,0 as balance ,sum(withdraw) as withdraw , name 
         FROM tbl_depositings  t1 join users t2 on t2.id=t1.user_id WHERE    t1.created_at between '".$start."' and '".$end."' GROUP BY t1.user_id");

      $all[]= DB::select("SELECT   sum(amount) as amount, name 
         FROM tbl_depositings  t1 join users t2 on t2.id=t1.user_id WHERE    t1.created_at between '".$start."' and '".$end."' group by user_id");
        $all[]= DB::select("SELECT   sum(withdraw) as withdraw , name 
         FROM tbl_depositings  t1 join users t2 on t2.id=t1.user_id WHERE    t1.created_at between '".$start."' and '".$end."' group by user_id ");
        $all[]= DB::select("SELECT   sum(withdraw) as matumizi 
         FROM tbl_depositings  t1 join users t2 on t2.id=t1.user_id WHERE    t1.created_at between '".$start."' and '".$end."'  ");
        $all[]= DB::select("SELECT  sum(amount) as makusanyo 
         FROM tbl_depositings  t1 join users t2 on t2.id=t1.user_id WHERE    t1.created_at between '".$start."' and '".$end."'  ");

         return $all;
}

    public function getDepositing_lists(Request $request)
    {
        $start=$request['start_date'];
        $end=$request['end_date'];
      $all[]= DB::select("SELECT  t1.*,sum(amount) as amount,sum(balance) as balance ,sum(withdraw) as withdraw ,concat(t2.first_name,' ',t2.middle_name, ' ',t2.last_name, '#',
        t2.medical_record_number) as name,t2.gender
         FROM tbl_depositings  t1 join tbl_patients t2 on t2.id=t1.patient_id WHERE    t1.created_at between '".$start."' and '".$end."' GROUP BY t1.visit_id");
        $all[]=  DB::select("SELECT  t1.*,sum(amount) as deposited,sum(balance) as balance ,sum(withdraw) as used ,concat(t2.first_name,' ',t2.middle_name, ' ',t2.last_name, '#',
        t2.medical_record_number) as name,t2.gender
         FROM tbl_depositings  t1 join tbl_patients t2 on t2.id=t1.patient_id WHERE   t1.created_at between '".$start."' and '".$end."' GROUP BY t1.patient_id,t1.facility_id");
        return $all;

}
public function deposit_summary_view(Request $request)
    {
        $visit_id=$request->visit_id;
        $all[]=  DB::select("SELECT  t1.*,sum(amount) as amount,sum(balance) as balance ,sum(withdraw) as withdraw ,concat(t2.first_name,' ',t2.middle_name, ' ',t2.last_name, '#',
        t2.medical_record_number) as name,t2.gender,t1.created_at as dated,t1.control_in as type
         FROM tbl_depositings  t1 join tbl_patients t2 on t2.id=t1.patient_id WHERE   t1.visit_id='".$visit_id."' GROUP BY t1.id");
$all[]=  DB::select("SELECT  t1.*,sum(amount) as deposited,sum(balance) as balance ,sum(withdraw) as used ,concat(t2.first_name,' ',t2.middle_name, ' ',t2.last_name, '#',
        t2.medical_record_number) as name,t2.gender,t1.created_at as dated,t1.control_in as type
         FROM tbl_depositings  t1 join tbl_patients t2 on t2.id=t1.patient_id WHERE   t1.visit_id='".$visit_id."' GROUP BY t1.visit_id");
return $all;

}



    public function showSearchFordeposit(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $search = $request->input('searchKey');
//        $patients_returned=Tbl_patient::where('medical_record_number','like','%'.$searchKey.'%')
//        ->orWhere('mobile_number','like','%'.$searchKey.'%')
//
//        ->get();
        $limit = 10;
        $sql = "select * from tbl_patients where medical_record_number like '%".$search."%'	or first_name like '%".$search."%'or
		last_name like '%".$search."%'  GROUP BY id limit ".$limit;
        $patients_returned = DB::select(DB::raw($sql));
        
DB::statement(" 
CREATE TABLE IF NOT EXISTS  `tbl_depositings` (
   `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `patient_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `withdraw` decimal(10,2) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT NULL,
  `control_in` varchar(11) DEFAULT NULL,
  `control` varchar(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
 `updated_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL
) ");

        return $patients_returned;
    }

    public function searchpatientForBill(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $search = $request->input('searchKey');
        $limit = 10;
        $sql = "select id as patient_id ,tbl_patients.* from tbl_patients where search_field like '%".$search."%' limit ".$limit;
        $patients_returned = DB::select(DB::raw($sql));
        return $patients_returned;

    }
}