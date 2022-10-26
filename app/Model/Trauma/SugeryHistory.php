<?php
namespace App\Model\Trauma;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurgeryHistory extends Model
{	
	
    protected $table = "tbl_trauma_history_surgeries";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');
	
	public static $rules = [
        "client_id"       => "required:tbl_trauma_history_surgeries",
        "concept_value"   => "required:tbl_trauma_history_surgeries",
    ];
	
	public static $create_rules = [
        "client_id"       => "required:tbl_trauma_history_surgeries",
        "concept_value"   => "required:tbl_trauma_history_surgeries",
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
		$records = SurgeryHistory::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));
		
		return $records->count() > 0 ? True : False;
	}
	
	/**
     * Returns the survey of a particular client
     * 
	 * @new is an object with all fillable fields in the model
     */
    public function getSurgeryHistory($client_id){
		return SurgeryHistory::with("concept")->where("client_id","=",$client_id)->get();
	}
}