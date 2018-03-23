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
    public function convertToXML($jsonString)
    {
        $jsonArray = json_decode($jsonString, true);
        $dom = new \DOMDocument('1.0', 'utf-8');
        $rootNode = $dom->createElement("data");
        $dom->appendChild($rootNode);
        $this->arrayToXML($jsonArray, $rootNode, $dom);
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    private function arrayToXML($array, \DOMNode $domNode, \DOMDocument $domDocument)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $node = null;
                if (is_int($key)) {
                    $key = "e" . $key;
                }
                $node = $domDocument->createElement($key);
                $domNode->appendChild($node);
                $this->arrayToXML($value, $node, $domDocument);
            }
        } else {
            $domNode->appendChild($domDocument->createTextNode($array));
        }

    }
}