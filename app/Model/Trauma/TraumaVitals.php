<?php
namespace App\Model\Trauma;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TraumaVitals extends Model
{	
	
    protected $table = "tbl_trauma_vitals";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');
	
	public static $rules = [
        "client_id"   	=> "required:tbl_trauma_vitals",
    ];
	
	public static $create_rules = [
        "client_id"   	=> "required:tbl_trauma_vitals",
    ];

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
		$records = TraumaVitals::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));
		
		return $records->count() > 0 ? True : False;
	}
}