<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\admin\Tbl_permission_user;
use App\admin\Tbl_integrating_key;
class UsersRegistrationController extends Controller
{
	
	//
  

 public function check_password_logout(Request $request)
    {

        $user_id=$request['id'];
        $password=bcrypt($request['password']) ;
        User::where('id',$user_id)->update(['loggedIn'=>0]);
        return response()->json([
            'msg' => 'User Has Logged Out Now...',
            'status' => '1'
        ]);
    }
  public function adminRegistration(Request $data)
  
  {
$email=$data['email'];
$mobile_number=$data['mobile_number'];
$facility_id=$data['facility_id'];
/*$intergratingKeys=Tbl_integrating_key::where('api_type',3)->get();

  if(count($intergratingKeys) ==0){
	 
      return response()->json([
                           'data' =>'Please Set Central IP Address for this Facility',
                           'status' =>0
                       ]);
	  
  }
		      $base_urls=$intergratingKeys[0]->base_urls;
		      $private_keys=$intergratingKeys[0]->private_keys;
		      $public_keys=$intergratingKeys[0]->public_keys;
    */    $check= User::where('email',$email)
            ->where('mobile_number',$mobile_number)
            ->where('facility_id',$facility_id)
            ->get();
        if(count($check)==1){
         	  return response()->json([
                           'data' =>$email." "."Already Registered",
                           'status' =>0
                       ]);
			 }
        else{
             $user[]= User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile_number' => $data['mobile_number'],
                'facility_id' => $data['facility_id'],
                'proffesionals_id' => $data['proffesionals_id'],
                'gender' => $data['gender'],
                'password' => bcrypt($data['password']),
            ]);
			
	$user_id= $user[0]->id;
	Tbl_permission_user::create(["permission_id"=>52,"user_id"=>$user_id,"grant"=>1]);
			
				$foliolist_array=array();		
        $user_info=array();
        $diseases=array();
        $items_array =array();
        $entity_array["entities"]=array();
			foreach($user as $row) {
            $user_info['user_id']=$row->id;
            $user_info['name']=$row->name;
            $user_info['email']=$row->email;
            $user_info['mobile_number']=$row->mobile_number;
            $user_info['facility_id']=$row->facility_id;
            $user_info['proffesionals_id']=$row->proffesionals_id;
            $user_info['gender']=$row->gender;
            $user_info['password']=$row->password;         	         
            array_push($foliolist_array,$user_info);
			}
		
		/*$entity_array["entities"]=$foliolist_array;
        $data_string=json_encode($entity_array,JSON_PRETTY_PRINT);
			//return  $data_string;
			return self::DataSync($data_string,$base_urls,$private_keys,$public_keys);
			*/
          return response()->json([
                           'data' =>$email." "."Successfully Registered",
                           'status' =>200
                       ]);   
        }
           
        }

    
    public function user_registration(Request $data)
    {
$email=$data['email'];
$mobile_number=$data['mobile_number'];
$facility_id=$data['facility_id'];

/*$intergratingKeys=Tbl_integrating_key::where('facility_id',$facility_id)->where('api_type',3)->get();
	if(count($intergratingKeys) > 0){
		$base_urls=$intergratingKeys[0]->base_urls;
		$private_keys=$intergratingKeys[0]->private_keys;
		$public_keys=$intergratingKeys[0]->public_keys;
	}
*/
	$check= User::where('email',$email)
		//->where('mobile_number',$mobile_number)
		//->where('facility_id',$facility_id)
		->get();
	if(count($check)==1)
	{
		return  $email." "."Already Registered";
	}
	else{
		 $user= User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'mobile_number' => $data['mobile_number'],
			'facility_id' => $data['facility_id'],
			'proffesionals_id' => $data['proffesionals_id'],
			'gender' => $data['gender'],
			'password' => bcrypt($data['password']),
		]);
	
	/*if(isset($base_urls)){
		$foliolist_array=array();		
        $user_info=array();
        $diseases=array();
        $items_array =array();
        $entity_array["entities"]=array();
			foreach($user as $row) {
            $user_info['user_id']=$row->id;
            $user_info['name']=$row->name;
            $user_info['email']=$row->email;
            $user_info['mobile_number']=$row->mobile_number;
            $user_info['facility_id']=$row->facility_id;
            $user_info['proffesionals_id']=$row->proffesionals_id;
            $user_info['gender']=$row->gender;
            $user_info['password']=$row->password;         	         
            array_push($foliolist_array,$user_info);
			}
		
		$entity_array["entities"]=$foliolist_array;
        $data_string=json_encode($entity_array,JSON_PRETTY_PRINT);
			//return  $data_string;
			
		      
			return self::DataSync($data_string,$base_urls,$private_keys,$public_keys);
			
            //return  $facility_name." Was Successfully registered";
			

            //return $user->email. " Account Successful Created";
        }*/
	}
	return  response()->json(["status"=>200, "data"=>$user->email." Account Successful Created"]);
    }
	
	public function sendUserCentrally(Request $request){
	    $facility_id=$request['entities'][0]['facility_id'];
		$user_id=$request['entities'][0]['user_id'];
		$name=$request['entities'][0]['name'];
		$email=$request['entities'][0]['email'];
		$proffesionals_id=$request['entities'][0]['proffesionals_id'];
		$gender=$request['entities'][0]['gender'];
		$mobile_number=$request['entities'][0]['mobile_number'];
		$password=$request['entities'][0]['password'];
		
		if(User::where('id',$user_id)->get()->count() == 0){
			$sql="INSERT INTO users SET
     			id='".$user_id."',
                name ='".$name."',
                email ='".$email."',
                mobile_number ='".$mobile_number."',
                facility_id='".$facility_id."',
                proffesionals_id ='".$proffesionals_id."',
                gender ='".$gender."',
                password ='".$password."'";
		    $insert=DB::statement($sql);
						  return response()->json([
                           'data' =>$name.", Account Successful Created,and Synchronized Centrally",
                           'status' =>1
                       ]);
		
		}else{
		 return response()->json([
                           'data' =>$email.", already registered and Synchronized Centrally.",
                           'status' =>0
                       ]);
          			
		}
		
	}
	
	public static function DataSync($data_string,$base_urls,$private_keys,$public_keys)
    {
         $request=$base_urls.'/api/sendUserCentrally';
         $url=$base_urls.'/api/sendUserCentrally';
		
        $request_method = 'POST';				
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
		
	  
    }

	

    public function user_list($facility_id)
    {
        //return User::get();
        return DB::table('users')
            ->join('tbl_proffesionals','tbl_proffesionals.id','=','users.proffesionals_id')
            ->select('users.id','users.name','users.facility_id','users.proffesionals_id',
                'users.mobile_number',
                'users.email',
                'users.created_at',
                'users.updated_at',
                'tbl_proffesionals.prof_name')
            ->where('users.facility_id',$facility_id)
            ->get();
    }


    public function user_delete($id)
    {

        return User::destroy($id);

    }

    public function user_update(Request $request)
    {
         $id=$request->all();
        $id=$request['id'];

        return User::where('id',$id)->update([
            'name'=>$request['name'],
            'mobile_number'=>$request['mobile_number'],
            'proffesionals_id'=>$request['proffesionals_id'],
            'email'=>$request['email'],
        ]);
    }


     public function check_password(Request $request)
    {
        
        $user_id=$request['id'];
        $password=bcrypt($request['password']) ;
        User::where('id',$user_id)->update(['password'=>$password]);
        return response()->json([
            'msg' => 'User Password Has changed..',
            'status' => '1'
        ]);
    }

    public function reset_password(Request $request)
    {
        $user = $request['user'];
        $userData = $request['details'];
        if($userData['new_password'] !=$userData['confirm_password']){
            return response()->json([
                'data' => 'Password mismatch..Please re-type password correctly and click save to continue',
                'status' => '0'
            ]);
        }
        else if(strlen($userData['new_password'])<8){
            return response()->json([
                'data' => 'Password must have at least 8 characters',
                'status' => '0'
            ]);
        }
        else {
            //$user_id=$request['user_id'];
            $user_id=$user['id'];
            $password=bcrypt($userData['new_password']) ;
            User::where('id',$user_id)->update(['password'=>$password]);
            return response()->json([
                'data' => 'Password changed..use your new password on next login',
                'status' => '1'
            ]);
        }

    }
}