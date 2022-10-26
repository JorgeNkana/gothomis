<?php
namespace App\Model\Trauma;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TraumaClient extends Model
{	
	
    protected $table = "tbl_trauma_clients";

    protected $guarded = ["id"];
    protected $hidden  = array('pivot');
	
	public static $rules = [
        "surname"   	=> "required:tbl_trauma_clients",
        "first_name"  	=> "required:tbl_trauma_clients",
        "gender"   		=> "required:tbl_trauma_clients",
        "arrival_mode"   => "required:tbl_trauma_clients",
        "arrival_date"   => "required:tbl_trauma_clients",
    ];
	
	public static $create_rules = [
        "surname"   	=> "required:tbl_trauma_clients",
        "first_name"  	=> "required:tbl_trauma_clients",
        "gender"   		=> "required:tbl_trauma_clients",
        "arrival_mode"   => "required:tbl_trauma_clients",
        "arrival_date"   => "required:tbl_trauma_clients",
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
		$records = TraumaClient::where("id", "<>", $id)->get();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));
		
		return $records->count() > 0 ? True : False;
	}
}