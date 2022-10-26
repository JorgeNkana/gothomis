<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;
use App\classes\systemViews;

class AuthenticateController extends Controller
{
  public function __construct()
  {
    // Apply the jwt.auth middleware to all methods in this controller
    // except for the authenticate method. We don't want to prevent
    // the user from retrieving their token if they don't already have it
    $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    systemViews::useraccessLevel();
  }
  //----
  public function index()
  {
    // Retrieve all the users in the database and return them
    $users = User::all();
    return $users;
  } 


  public function getAuthenticatedUser()
  {
	 
	  
    try {

      if (! $user = JWTAuth::parseToken()->authenticate()) {
        return response()->json(['user_not_found'], 404);
      }

    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

      return response()->json(['token_expired'], $e->getStatusCode());

    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

      return response()->json(['token_invalid'], $e->getStatusCode());

    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

      return response()->json(['token_absent'], $e->getStatusCode());

    }

    // the token is valid and we have found the user via the sub claim
    return response()->json(compact('user'));
  }





  public function authenticate(Request $request)
  {
    $credentials = $request->only('email', 'password');
	
	 $online_users=User::where('email',$request->email)
		                  ->where('loggedIn',22)
		                  ->where('ip','!=',$_SERVER['REMOTE_ADDR'])->get()->count();
		

    try {
		
      // verify the credentials and create a token for the user
      if (! $token = JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'Invalid User name or Password'], 401);
      }
	  else if($token = JWTAuth::attempt($credentials) AND $online_users >1){
				return response()->json(['error' => 'Multiple login with single account detected'], 401);          
				
			}
    } catch (JWTException $e) {
      // something went wrong
      return response()->json(['error' => 'could_not_create_token'], 500);
    }

	   User::where('email',$request->email)->update(['loggedIn'=>1,'ip'=>$_SERVER['REMOTE_ADDR']]);
	
    // if no errors are encountered we can return a JWT
    return compact('token');
  }

  public function update_user(Request $request)
  {
    $id=$request['id'];
    return User::where('id',$id)->update($request->all());
  }
  public function register(Request $request)
  {

    $name=$request['name'];
    $email=$request['email'];
    $password=bcrypt($request['password']);
    return  User::create([
      'name'=>$name,
      'email'=>$email,
      'password'=>$password,

    ]);
  }
  public function delete($id)
  {

    return User::destroy($id);

  } 
  
 
  public function edit($id)
  {

    return User::where('id',$id)->first();
  }
}