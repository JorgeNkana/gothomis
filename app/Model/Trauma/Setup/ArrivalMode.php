<?php
namespace App\Model\Trauma\Setup;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArrivalMode extends Model
{	
	
    protected $table = "tbl_arrival_modes";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');
	
	public static $rules = [
        "mode"       => "required:tbl_arrival_modes",
    ];
	
	public static $create_rules = [
        "mode"       => "required:tbl_arrival_modes",
    ];

    /**
	 * Returns the Visits associated with the mode
	 */
	function arrivals()
    {
        return $this->hasMany("App\Models\Visits\Encounter", "arrival_mode_id");
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
		$records = ArrivalMode::get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));
		
		return $records->count() > 0 ? True : False;
	}
}