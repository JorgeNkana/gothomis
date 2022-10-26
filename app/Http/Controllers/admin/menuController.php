<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use DB;
class menuController extends Controller
{
	
    public function getUserMenu($user_id){

        $sql="SELECT t1.*,
 
 CASE WHEN t1.user_id IS NOT NULL  THEN (SELECT  t2.name FROM  users t2 WHERE t2.id=t1.user_id AND t2.id='".$user_id."' GROUP  BY t2.id) END AS name


FROM vw_user_access_level t1

             WHERE t1.user_id='".$user_id."'
               AND t1.allowed=1
               AND t1.is_it_allowed_to_access=1
                GROUP BY state_p 
                ORDER BY descr ASC
                LIMIT 40
             ";

        return DB::SELECT($sql);


		
	}

	public function getLoginUserDetails($user_id){

	return DB::table('vw_user_details')->where('user_id',$user_id)->get();



	}

	
	public function getAuthorization($user_id,$state_name){
		
	$authorization_number= DB::table('vw_user_access_level')
									->select('state_p','descr','user_type','icons')
									->where('user_id',$user_id)
									->where('state_p',$state_name)
									->where('allowed',1)
									->where('is_it_allowed_to_access',1)
									->orderBy('descr','ASC')			
									->get();
									
									return count($authorization_number);		
		
		
	}
	
	
	public function userMatrix($facility_id){
		$response = "<table style='border:thin solid black; width:100%'>"
					."<thead  style='text-align:center'>"
					."<tr>"
					."<td colspan='7'><h2  style='font-weight:bold;'>"
					.DB::select("select facility_name from tbl_facilities where id='".$facility_id."'")[0]->facility_name
					."</h2>"
					."<h3>USER ACCESS MATRIX AS OF ".dATE('Y-d-m')."</h3>"
					."</td>"
					."</tr>"
					."<tr>"
					."<td style ='border-bottom:thin solid black;border-right:thin solid black;border-top:thin solid black;font-weight:bold; width:5%;'>Sno</td>"
					."<td style ='border-bottom:thin solid black;border-right:thin solid black;border-top:thin solid black;font-weight:bold; width:15%;'>User</td>"
					."<td style ='border-bottom:thin solid black;border-right:thin solid black;border-top:thin solid black;font-weight:bold; width:15%;'>Designation</td>"
					."<td style ='border-bottom:thin solid black;border-right:thin solid black;border-top:thin solid black;font-weight:bold; width:15%;'>Login Name</td>"
					."<!--td style ='border-bottom:thin solid black;border-right:thin solid black;border-top:thin solid black;font-weight:bold; width:15%;'>Role</td-->"
					."<td style ='border-bottom:thin solid black;border-right:thin solid black;border-top:thin solid black;font-weight:bold; width:15%;'>Menu</td>"
					."<td style ='border-bottom:thin solid black;border-right:thin solid black;border-top:thin solid black;font-weight:bold; width:10%;'>Date Assigned</td>"
					."<td style ='border-bottom:thin solid black;border-top:thin solid black;font-weight:bold; width:10%;'>Signature</td>"
					."</tr>"
					."</thead>"
					."<tbody>";
		$users = DB::table('users')
					->join('tbl_proffesionals', 'users.proffesionals_id', 'tbl_proffesionals.id')
					->where('users.facility_id',$facility_id)
					->select('name','email','users.id','prof_name')
					->orderBy('name')->get();
		$Sno = 1;
		foreach($users as $user){
			$accesses = DB::select("select tbl_roles.title, tbl_permissions.title as menu,tbl_permission_users.created_at from tbl_permission_users join tbl_permissions on tbl_permission_users.user_id='".$user->id."' and tbl_permission_users.grant=1 and tbl_permission_users.permission_id = tbl_permissions.id join tbl_permission_roles on tbl_permissions.id = tbl_permission_roles.permission_id join tbl_roles on tbl_roles.id=tbl_permission_roles.role_id group by tbl_permission_users.permission_id, tbl_permission_users.user_id order by tbl_roles.title,  tbl_permissions.title asc");
			$span = (count($accesses)==0 ? 1 : count($accesses));
			
			$response .= "<tr>"
					."<td valign='top' rowspan='$span'"
					." style='border-bottom:thin dotted black;border-right:thin dotted black;; text-align:right'>"
					.$Sno++.".</td>"
					."<td valign='top' rowspan='$span'"
					." style='border-bottom:thin dotted black;border-right:thin dotted black;'>"
					.strtoupper($user->name)."</td>"
					."<td valign='top' rowspan='$span'"
					." style='border-bottom:thin dotted black;border-right:thin dotted black;'>"
					.strtoupper($user->prof_name)."</td>"
					."<td valign='top' rowspan='$span'"
					." style='border-bottom:thin dotted black;border-right:thin dotted black;'>"
					.$user->email."</td>";
			if(count($accesses) == 0)
				$response .= "<!--td style='border-bottom:thin dotted black;border-right:thin dotted black;'>&nbsp;</td-->"
							."<td style='border-bottom:thin dotted black;border-right:thin dotted black;'>&nbsp;</td>"
							."<td style='border-bottom:thin dotted black;border-right:thin dotted black;'>&nbsp;</td>"
							."<td style='border-bottom:thin dotted black;'>&nbsp;</td>"
							."</tr>";
			else{
				$response .= "<!--td style='border-bottom:thin dotted black;border-right:thin dotted black;'>"
							.$accesses[0]->title."</td-->"
							."<td style='border-bottom:thin dotted black;border-right:thin dotted black;'>"
							.$accesses[0]->menu."</td>"
							."<td style='border-bottom:thin dotted black;border-right:thin dotted black;'>"
							.$accesses[0]->created_at."</td>"
							."<td rowspan='$span' style='border-bottom:thin dotted black;'>&nbsp;</td>"
							."</tr>";
				for($i = 1; $i < count($accesses); $i++){
					$response .= "<tr><!--td style='border-bottom:thin dotted black;border-right:thin dotted black;'>"
							.$accesses[$i]->title."</td-->"
							."<td style='border-bottom:thin dotted black;border-right:thin dotted black;'>"
							.$accesses[$i]->menu."</td>"
							."<td style='border-bottom:thin dotted black;border-right:thin dotted black;'>"
							.$accesses[$i]->created_at."</td></tr>";
				}
			}
		}
		$response .="</tbody></table>";
		$response .="<br /><br />";
		$response .="<table style='font-weight:bold'>"
					."<tr><td>List verified by: ______________________</td>"
					."<td>&nbsp;&nbsp;&nbsp;Date: _______________________</td></tr></table>";
		
		return $response;
	}
	
}