<?php
/**
 * Created by PhpStorm.
 * User: elama
 * Date: 21.03.18
 * Time: 14:47
 */

namespace App\Service;


use App\Entity\JSONData;
use Doctrine\ORM\EntityManager;

class JSONDataService
{
    private $em;
    private $jsonValidator;

    public function __construct( EntityManager $entityManager )
    {
        $this->em            = $entityManager;
        $this->jsonValidator = new JSONValidator();
    }

    public function create( $data, $deleteAfterAccess)
    {
        $json = $this->jsonValidator->tryParse( $data );
        if ( ! $json) {
            return false;
        }
        $jsonData = new JSONData( $json, $deleteAfterAccess );
        try {
            $this->em->persist( $jsonData );
            $this->em->flush();
            return $jsonData;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function get( $url )
    {

        /** @var JSONData $result */
        $result = $this->em->getRepository( JSONData::class )->findOneBy( [ 'url' => $url ] );
        if ($result && ! $result->isDeleted()) {
            $result->incrementDownloadAmount();
            if ($result->getDeleteAfterFirstAccess()) {
                $result->delete();
            }
            try {
                $this->em->flush();
                return $result;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete( $url )
    {
        $result = $this->em->getRepository( JSONData::class )->findOneBy( [ 'url' => $url ] );

        if ($result) {
            $result->delete();
            try {
                $this->em->flush();
                return $result;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    public function update( $url, $newData )
    {
        $result = $this->em->getRepository( JSONData::class )->findOneBy( [ 'url' => $url ] );
        $json   = $this->jsonValidator->tryParse( $newData );
        if (! $json || ! $result || $result->isDeleted()) {
            return false;
        }
        $result->setData( $json );
        try {
            $this->em->flush();
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function convertToXML( $inputText )
    {
        $jsonText = $this->jsonValidator->tryParse( $inputText );
        if ( ! $jsonText) {
            return false;
        }
        $jsonArray = json_decode($jsonText);
        $xml = XMLConverter::convertToXML($jsonArray);
        return $xml;
    }

}