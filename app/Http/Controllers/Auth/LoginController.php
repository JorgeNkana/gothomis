<?php

namespace App\Http\Controllers\Auth;
use App\User;
use App\User\Tbl_last_login;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
	 public function logout($id)
  {
	    $last_login=User::where('id',$id)->get();
		$ip=$last_login[0]->ip;
		
		// Turn on output buffering  
ob_start();  

//Get the ipconfig details using system commond  
system('ipconfig /all');  

// Capture the output into a variable  
$mycomsys=ob_get_contents();  

// Clean (erase) the output buffer  
ob_clean();  

$find_mac = "Physical"; 
//find the "Physical" & Find the position of Physical text  

$pmac = strpos($mycomsys, $find_mac);  
// Get Physical Address  

$macaddress=substr($mycomsys,($pmac+36),17);  
//Display Mac Address  
		
		
		
		Tbl_last_login::create(['user_id'=>$id,'ip'=>$ip,'mac_address'=>$macaddress]);

        return User::where('id',$id)->update(['loggedIn'=>0]);

  }
}
