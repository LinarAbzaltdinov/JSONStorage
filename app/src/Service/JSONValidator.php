<?php
/**
 * Created by PhpStorm.
 * User: elama
 * Date: 19.03.18
 * Time: 16:07
 */

namespace App\Service;


class JSONValidator
{
    private function validate( $string )
    {
        return is_string( $string )
               && is_array( json_decode( $string, true ) )
               && ( json_last_error() == JSON_ERROR_NONE ) ? true : false;
    }

    public function tryParse( $string )
    {
        if ($this->validate( $string )) {
            return str_replace( "\r\n", "", $string );
        }
        return null;
    }
}