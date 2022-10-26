<?php
namespace App\Model\Trauma\Setup;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TraumaConcept extends Model
{	
	
    protected $table = "tbl_trauma_concepts";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');
	
	public static $rules = [
        "concept_name"       => "required:tbl_trauma_concepts",
        "section_code"       => "required|unique:tbl_trauma_concepts",
        "sub_section_code"   => "required|unique:tbl_trauma_concepts",
    ];
	
	public static $create_rules = [
        "concept_name"       => "required:tbl_trauma_concepts",
        "section_code"       => "required:tbl_trauma_concepts",
        "section_name"       => "required:tbl_trauma_concepts",
        "sub_section_code"   => "required:tbl_trauma_concepts",
        "sub_section_name"   => "required:tbl_trauma_concepts",
    ];

    /**
	 * Returns the ED Surveys associated with the concept
	 */
	function surveys()
    {
        return $this->hasMany("App\Model\Trauma\PrimarySurvey", "concept_id");
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
    public static function isDuplicate($new){
		$records = TraumaConcept::get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));
		
		return $records->count() > 0 ? True : False;
	}
	
	public static function getConceptBySection(){
		$sections = TraumaConcept::select("section_code")
									->distinct()
									->get();
		
		$concepts = ["UNSPECIFIED"=>"UNSPECIFIED"];
		foreach($sections as $section){
			$concepts[$section->section_code] = [];
			$sub_sections = TraumaConcept::where("section_code","=",$section->section_code)
											->select("sub_section_code")
											->distinct()
											->get();
			foreach($sub_sections as $sub_section){
				$concepts[$section->section_code][$sub_section->sub_section_code] = 
								TraumaConcept::where("section_code","=",$section->section_code)
											->where("sub_section_code","=",$sub_section->sub_section_code)
											->get();
			}
		}
		
		return $concepts;
	}
}