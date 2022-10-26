<?php

namespace App\Http\Controllers\Trauma;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Trauma\PrimarySurvey;
use DB;

class PrimarySurveyController extends Controller
{
    /*
	private $model;
	
	public __construct()
	{
		
		$this->model = new PrimarySurvey();
		
	}
	*/
	
	
	/**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {

        return PrimarySurvey::getSurvey($request->client_id);

    }

 

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $data      =  $request->all();
		if(!is_array($data))
			return customApiResponse($data, "INVALID REQUEST DATA. {".GETTYPE($data)."} TYPE RECEIVED, WHEREAS AN {ARRAY} WAS EXPECTED", 402);
		
        $validator =  Validator::make($data, PrimarySurvey::$create_rules);
        if ($validator->fails()) {
            return customApiResponse($data, "VALIDATION ERRORS", 402, $validator->errors()->all());
        }
		
		foreach($data as $datum){
			if(PrimarySurvey::isDuplicate((array)$datum))
				return customApiResponse($datum, "DUPLICATE! SIMILAR RECORD ALREADY EXISTS", 403);
		}
		
		$error = false;
        foreach($data as $datum){
			if(!$result = PrimarySurvey::create($data))
				$error = true;
		}
		
        if(!$error) {
            return customApiResponse($result, 'Entry successfully recorded', 201);
        } else {
            return customApiResponse($data, 'ERROR', 400);
        }

    }

 

    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        
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