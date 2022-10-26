<?php
namespace App\Model\Trauma;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalHistory extends Model
{	
	
    protected $table = "tbl_trauma_history_medical";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');
	
	public static $rules = [
        "client_id"       => "required:tbl_trauma_history_medical",
        "concept_value"   => "required:tbl_trauma_history_medical",
    ];
	
	public static $create_rules = [
        "client_id"       => "required:tbl_trauma_history_medical",
        "concept_value"   => "required:tbl_trauma_history_medical",
    ];

    /**
	 * Returns the Client to who the survey belongs
	 */
	function client()
    {
        return $this->belongsTo("App\Model\Trauma\Client", "client_id");
    }

	/**
	 * Returns the concept related
	 */
	function concept()
    {
        return $this->belongsTo("App\Model\Trauma\Setup\TraumaConcept", "concept_id");
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
		$records = MedicalHistory::where("id", "<>", $id)->get();
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
		return MedicalHistory::with("concept")->where("client_id","=",$client_id)->get();
	}
}