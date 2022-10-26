<?php
/**
 * Created by PhpStorm.
 * User: Mazigo Jr
 * Date: 2017-11-30
 * Time: 5:22 PM
 */

namespace App\classes;


class CHF_Settings
{
    public static function GetCHFceilling()
    {
        return response()->json(
            [
                "use_chf_settings"=>0,
                "chf_ceiling"=>20000,
            ]
        );
}
}