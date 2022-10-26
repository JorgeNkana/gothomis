<?php

namespace App\Http\Controllers\Exemption;

use App\Exemption\Tbl_exemption_access;
use App\Exemption\Tbl_exemptions_type;
use App\Exemption\Tbl_exemption;
use App\Exemption\Tbl_referral_institution;
use App\Exemption\Tbl_violence_category;
use App\Exemption\Tbl_violence_type;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ExemptionTypeController extends Controller
{
    //
    public function exemption_type_registration(Request $request)
    {
        $exempt_type=$request['exemption_name'];

       $check= Tbl_exemptions_type::where('exemption_name',$exempt_type)
           ->get();
        if(count($check)==1)
        {
            return  $exempt_type." "."Already Registered";
        }
        else{
            Tbl_exemptions_type::create($request->all());

            return  "SuccessFul!!!..";
        }

    }

    public function exemption_type_list($user_id)
    {
        //return Tbl_exemptions_type::get();
        return DB::table('tbl_payments_categories')
            ->join('tbl_pay_cat_sub_categories','tbl_pay_cat_sub_categories.pay_cat_id','=','tbl_payments_categories.id')
            ->join('tbl_exemption_accesses','tbl_pay_cat_sub_categories.id','=','tbl_exemption_accesses.exempt_id')
            ->where('tbl_exemption_accesses.user_id',$user_id)
            ->where('tbl_exemption_accesses.status',1)
            ->select('tbl_pay_cat_sub_categories.id as id','tbl_pay_cat_sub_categories.sub_category_name as exemption_name','tbl_pay_cat_sub_categories.pay_cat_id')
            ->get();
    }


    public function Remove_user_Exemption_access($id)
    {

        return Tbl_exemption_access::where('id',$id)->update(['status'=>0]);
    }


    public function SelectedUserWithExemptionAccess($user_id)
    {
        return DB::table('tbl_payments_categories')
            ->join('tbl_pay_cat_sub_categories','tbl_pay_cat_sub_categories.pay_cat_id','=','tbl_payments_categories.id')
            ->join('tbl_exemption_accesses','tbl_pay_cat_sub_categories.id','=','tbl_exemption_accesses.exempt_id')
            ->where('tbl_exemption_accesses.user_id',$user_id)
            ->where('tbl_exemption_accesses.status',1)
            ->select('tbl_exemption_accesses.id as access_id','tbl_pay_cat_sub_categories.id as id','tbl_pay_cat_sub_categories.sub_category_name as exemption_name','tbl_pay_cat_sub_categories.pay_cat_id')
            ->get();
    }
    public function exemption_type_s()
    {
        //return Tbl_exemptions_type::get();
        return DB::table('tbl_payments_categories')
            ->join('tbl_pay_cat_sub_categories','tbl_pay_cat_sub_categories.pay_cat_id','=','tbl_payments_categories.id')
            ->where('tbl_pay_cat_sub_categories.pay_cat_id',3)
            ->select('tbl_pay_cat_sub_categories.id as id','tbl_pay_cat_sub_categories.sub_category_name as exemption_name','tbl_pay_cat_sub_categories.pay_cat_id')
            ->get();
    }


    public function exemption_type_delete($id)
    {
$check=Tbl_exemption::where('exemption_type_id',$id)->get();
        if(count($check)==1)
        {
            return response()->json([
                'msg'=>'Exemption Type is IN USE....Can not be Deleted..',
                'status'=>0
            ]);
        }
        Tbl_exemptions_type::destroy($id);
        return response()->json([
            'msg'=>'Item Deleted...',
            'status'=>1
        ]);
    }

    public function exemption_type_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_exemptions_type::where('id',$id)->update($request->all());
    }



    public function institution_registration(Request $request)
    {
       $institute_name=$request['institution_name'] ;
       $institute_type=$request['institution_type'] ;
        $dup=Tbl_referral_institution::where('institution_name',$institute_name)->
        where('institution_type',$institute_type)
            ->get();
        if($institute_name==""){
        return response()->json([
            'msg'=>'Please institution name',
            'status'=>0
        ]);
    } else if($institute_type==""){


        return response()->json([
            'msg'=>'Please institution type',
            'status'=>0
        ]);
    }
        else if(count($dup)>0){
            return response()->json([
                'msg'=>$institute_name.' Already exists',
                'status'=>0
            ]);
        }
    else{
    $data=Tbl_referral_institution::create($request->all());
        return response()->json([
            'msg'=>$institute_name.' Created Successful..',
            'status'=>1
        ]);
}
}


    public function institution_list()
    {
      return Tbl_referral_institution::get();
    }

    public function institution_update(Request $request)
    {
        $id=$request['id'];
          Tbl_referral_institution::where('id',$id)->update($request->all());
        return response()->json([
            'msg'=>' Updated Successful..',
            'status'=>1
        ]);
    }




public function violence_cat_registration(Request $request)
    {
        $violence_cat_name=$request['violence_type_category'] ;
        $dup=Tbl_violence_category::where('violence_type_category',$violence_cat_name)->get();
        if($violence_cat_name==""){
        return response()->json([
            'msg'=>'Please violence category name',
            'status'=>0
        ]);
    }

        else if(count($dup)>0){
        return response()->json([
            'msg'=>$violence_cat_name.' Already exists',
            'status'=>0
        ]);
        }
    else{

    $data=Tbl_violence_category::create($request->all());
        return response()->json([
            'msg'=>$violence_cat_name.' Created Successful..',
            'status'=>1
        ]);
}
}



    public function violence_cat_list()
    {
      return Tbl_violence_category::get();
    }

    public function violence_cat_update(Request $request)
    {
        $id=$request['id'];
          Tbl_violence_category::where('id',$id)->update($request->all());
        return response()->json([
            'msg'=>' Updated Successful..',
            'status'=>1
        ]);
    }

    public function violence_type_list()
    {
      return Tbl_violence_type::get();
}
}