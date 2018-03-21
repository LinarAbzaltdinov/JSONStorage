<?php
/**
 * Created by PhpStorm.
 * User: elama
 * Date: 21.03.18
 * Time: 17:00
 */

namespace App\Service;


class XMLConverter
{
    static function convertToXML( $jsonArray )
    {
        $xml = new \SimpleXMLElement( '<data/>' );
        array_walk_recursive( $jsonArray, array( $xml, 'addChild' ) );
        return $xml->asXML();
    }

}