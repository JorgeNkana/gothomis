<?php
namespace App\Model\Trauma;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjuryDetail extends Model
{	
	
    protected $table = "tbl_trauma_injury_details";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');
	
	public static $rules = [
        "client_id"       => "required:tbl_trauma_injury_details",
        "concept_id"       => "required:tbl_trauma_injury_details",
    ];
	
	public static $create_rules = [
        "client_id"       => "required:tbl_trauma_injury_details",
        "concept_id"      => "required:tbl_trauma_injury_details",
        "concept_value"   => "required:tbl_trauma_injury_details",
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
		$records = InjuryDetail::where("id", "<>", $id)->get();
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
		$survey = [];
		$sub_sections = TraumaConcept::where("section_code","=","INJURY_DETAILS")->get();
									
        foreach($sub_sections as $section){
			$entries = InjuryDetail::where("concept_id","=",$section->id)
									 ->where("client_id","=",$client_id)
									 ->get();
			if(count($entries) > 0){
				$survey[] = [$section->sub_section_name=>new \stdClass()];
				foreach($entries as $entry){
					$survey[$section->sub_section_name]->name = $section->concept_name;
					$survey[$section->sub_section_name]->value = $entry->concept_value;
				}
			}
		}
		
		return $survey;
	}
}