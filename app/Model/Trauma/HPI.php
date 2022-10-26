<?php
namespace App\Model\Trauma;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HPI extends Model
{	
	use SoftDeletes;
	
    protected $table = "tbl_trauma_hpi";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');
	
	public static $rules = [
        "client_id"       			=> "required:tbl_trauma_hpi",
        "date_of_injury"   			=> "required:tbl_trauma_hpi",
        "place_of_injury"   		=> "required:tbl_trauma_hpi",
        "pre_hospital_care"   		=> "required:tbl_trauma_hpi",
        "activity_during_injury"   	=> "required:tbl_trauma_hpi",
    ];
	
	public static $create_rules = [
        "client_id"       			=> "required:tbl_trauma_hpi",
        "date_of_injury"   			=> "required:tbl_trauma_hpi",
        "place_of_injury"   		=> "required:tbl_trauma_hpi",
        "pre_hospital_care"   		=> "required:tbl_trauma_hpi",
        "activity_during_injury"   	=> "required:tbl_trauma_hpi",
    ];

    /**
	 * Returns the Client to who the survey belongs
	 */
	function client()
    {
        return $this->belongsTo("App\Model\Trauma\Client", "client_id");
    }


	/**
	 * Upper case fields for comparison in the isDuplicate function
	 *
	 */
	public function getNameAttribute($value)
    {
        return strtoupper($value);
    }	
	
	public function getCodeAttribute($value)
    {
        return strtoupper($value);
    }	
	
	/**
     * Checks for duplication of record
     * 
	 * @new is an object with all fillable fields in the model
     */
    public static function isDuplicate($new, $id = 0){
		$records = HPI::where("id", "<>", $id)->all();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));
		
		return $records->count() > 0 ? True : False;
	}
	
	/**
     * Returns the survey of a particular client
     * 
	 * @new is an object with all fillable fields in the model
     */
    public static function getSurvey($client_id){
		return HPI::where("client_id","=",$client_id)->get();
	}
}