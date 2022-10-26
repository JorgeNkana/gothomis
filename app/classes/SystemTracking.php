<?php
namespace App\classes;
use App\Trackable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 2018-03-05
 * Time: 4:19 PM
 */
class SystemTracking
{
    public static function Tracking($user_id,$patient_id,$trackable_id,$new_value,$old_value)
    {
        $url= App::make(Route::class)->getActionName(true);
        $action=Str::snake(str_replace(['post','get','patch','put','delete'],'',last(Str::parseCallback($url,null))),'-');
        if (isset($old_value)){
            $model_affected= get_class($old_value);
        }
        if (isset($new_value)){
            $model_affected= get_class($new_value);
        }
        Trackable::create([
            'user_id' => $user_id,
            'patient_id' => $patient_id,
            'trackable_id' => $trackable_id,
            'action' => $action,
            'trackable_type' => $model_affected,
            'new_value' =>$new_value,
            'old_value' => $old_value,
        ]);
    }
}