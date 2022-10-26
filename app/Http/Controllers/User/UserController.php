<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Validator;

class UserController extends Controller
{
    /**
     * Display the all Users.
     * @param  Request  $request
     * @return customApiResponse
     */
    public function index (Request $request){
        $data     =  $request->all();
        $per_page =  (isset($data['per_page'])? $data['per_page'] : 15);
        $users    =  User::all();
        $users    =  customPaginate($users, $per_page);
        return customApiResponse($users);
    }

    /**
     * create an User.
     * @param  Request  $request
     * @return customApiResponse
     */
    public function create(Request $request){
        $data      =  $request->all();
        $validator =  Validator::make($data, User::$create_rules);

        if ($validator->fails()) {
            return customApiResponse($data, "Validation Error", 400, $validator->errors()->all());
        }

        $result = User::create($data);

        if($result) {
            return customApiResponse($result, 'SUCCESSFULLY_CREATED', 201);
        } else {
            return customApiResponse($data, 'ERROR', 500);
        }
    }

    /**
     * get an User.
     * @param  int  $id
     * @return customApiResponse
     */
    public function show($id)
    {
        $user = User::find($id);
        if ($user == null) {
            return customApiResponse($user, 'User Not Found', 404, 'User Not Found');
        }
        return customApiResponse($user, 'SUCCESSFULL');
    }

    /**
     * update an User.
     * @param  Request  $request
     * @param  int  $id
     * @return customApiResponse
     */
    public function update(Request $request, $id)
    {
        $data      = $request->all();
        $validator = Validator::make($data, User::$rules);

        if ($validator->fails()) {
            return customApiResponse($data, 'Validation error', 400, $validator->errors()->all());
        }

        $user =  User::find($id);
        if ($user == null) {
            return customApiResponse($user, 'User Not Found', 404, 'User Not Found');
        }

        $result =  $user->update($data);

        if ($result) {
            return customApiResponse($result, 'SUCCESSFULLY_UPDATED', 200);
        } else {
            return customApiResponse($data, 'Error updating User ', 500);
        }
    }

    /**
     * destroy/delete an User.
     * @param  Request  $request
     * @return customApiResponse
     */
    public function destroy($id){
        $user =  User::find($id);

        if ($user == null) {
            return customApiResponse($user, 'User Not Found', 404, 'User Not Found');
        }

        if ($user->delete()) {
            return customApiResponse($user, 'SUCCESSFULLY_DELETED', 200);
        } else {
            return customApiResponse($user, 'Error Deleting User', 500);
        }
    }
    //
}