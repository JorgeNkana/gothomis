<?php

use App\Model\Trackable;

/**
 * Tracks user's activities on models
 *
 * @param Model $trackable the model object we're tracking
 * @param Model $user the user performing the action on the model
 * @param String $action the action being performed on the model
 */
 
if (!function_exists('track_activity')) {
     function track_activity($trackable, $user, $action){
        Trackable::create(array(
            'user_id'        => $user->id,
            'action'         => $action,
            'trackable_id'   => $trackable->id,
            'trackable_type' => get_class($trackable),
        ));
    }
}

if (!function_exists('action_name')) {
    function action_name(){
     return (isset($_SERVER['HTTPS']) ? "https" : "http") ."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
}