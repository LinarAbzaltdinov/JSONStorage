<?php

namespace App\Controller;

use App\Service\JSONDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DataController extends AbstractController
{
    const SERVICE_ERROR = "Неизвестная ошибка";
    const CLIENT_ERROR  = "Данные от клиента не получены";
    const NODATA_ERROR  = "Данные не найдены";

    /**
     * @Route(methods={"POST"}, path="/upload")
     */
    public function createData( Request $request, JSONDataService $JSONDataService )
    {
        $inputText         = $request->request->get( "text" );
        $deleteAfterAccess = $request->request->get( "deleteAfterAccess" );
        if ( ! $inputText) {
            return $this->render( "error.html.twig", [ "errorMessage" => self::CLIENT_ERROR ] );
        }
        $result = $JSONDataService->create( $inputText, $deleteAfterAccess );
        if ($result) {
            return $this->render( 'showLink.html.twig', [ 'url' => $result->getUrl() ] );
        }
        return $this->render( "error.html.twig", [ "errorMessage" => self::SERVICE_ERROR ] );
    }

    /**
     * @Route(methods={"GET"}, path="/files/{url}")
     */
    public function getData( $url, JSONDataService $JSONDataService )
    {
        $result = $JSONDataService->get( $url );

        if ($result) {
            return $this->render( "showJson.html.twig",
                [ "json"     => json_encode( json_decode( $result->getData() ), JSON_PRETTY_PRINT ),
                  "filename" => $result->getId() . '.json',
                  "url"      => $result->getUrl() ] );
        }
        return $this->render( "error.html.twig", [ "errorMessage" => self::NODATA_ERROR ] );
    }

    /**
     * @Route(methods={"DELETE"}, path="/files/{url}")
     */
    public function deleteData( $url, JSONDataService $JSONDataService )
    {
        $result = $JSONDataService->delete( $url );

        if ($result) {
            return new JsonResponse( [ "status" => true ] );
        }
        return new JsonResponse( [
            "status"       => false,
            "errorMessage" => self::SERVICE_ERROR,
        ] );
    }

    /**
     * @Route(methods={"PUT"}, path="/files/{url}")
     */
    public function updateData( $url, Request $request, JSONDataService $JSONDataService )
    {
        $newData = $request->get( 'text' );
        if ( ! $newData) {
            return new JsonResponse( [
                "status"       => false,
                "errorMessage" => self::CLIENT_ERROR,
            ] );
        }
        $result = $JSONDataService->update( $url, $newData );
        if ($result) {
            return new JsonResponse( [ "status" => true ] );
        }
        return new JsonResponse( [
            "status"       => false,
            "errorMessage" => self::SERVICE_ERROR,
        ] );
    }

    /**
     * @Route(methods={"POST"}, path="/files/{url}/xml")
     */
    public function convertToXML( Request $request, JSONDataService $JSONDataService )
    {
        $inputText = $request->request->get( 'text' );
        $xmlData   = $JSONDataService->convertToXML( $inputText );
        if ($xmlData) {
            return $this->render( "showXml.html.twig",
                [
                    "data"     => $xmlData,
                    "filename" => 'temp.xml',
                ] );
        }
        return $this->render( "error.html.twig", [ "errorMessage" => self::NODATA_ERROR ] );
    }
}