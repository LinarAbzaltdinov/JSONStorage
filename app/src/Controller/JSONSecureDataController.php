<?php

namespace App\Controller;

use App\Entity\JSONData;
use App\Entity\JSONSecureData;
use App\Service\JSONDataService;
use App\Service\PasswordGenerator;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class JSONSecureDataController extends AbstractJSONDataController
{
    const NOT_SECURE_DATA_ERROR = "Данные не найдены. Ваш URL не должен содержать /private.";
    const NOT_AVAILIABLE_ERROR = "Доступ зарпещен";
    /**
     * @Route(methods={"GET"}, path="/files/private/{url}")
     */
    public function getData($url, Request $request, JSONDataService $JSONDataService)
    {
        $checkResult = $this->checkCredentials( $url, $request, $JSONDataService );
        if ($checkResult !== true) {
            return $this->errorResponse(self::NOT_SECURE_DATA_ERROR);;
        }
        $jsonDataObject = $JSONDataService->get( $url );
        $JSONDataService->incrementDownloadAmount( $jsonDataObject );
        return $this->render( "showJson.html.twig",
            [
                "data"     => $jsonDataObject->getPrettyPrintData(),
                "filename" => $jsonDataObject->getId() . '.json',
                "url"      => $jsonDataObject->getUrl()
            ] );
    }

    /**
     * @Route(methods={"DELETE"}, path="/files/private/{url}")
     */
    public function deleteData($url, Request $request, JSONDataService $JSONDataService)
    {
        $checkResult = $this->checkCredentials( $url, $request, $JSONDataService );
        if ($checkResult !== true) {
            return $this->createJsonResponse( false,  self::NOT_AVAILIABLE_ERROR);
        }
        $jsonDataObject = $JSONDataService->delete( $url );
        if ( ! $jsonDataObject) {
            return $this->createJsonResponse( false, self::SERVICE_ERROR );
        }
        return $this->createJsonResponse( true );
    }

    /**
     * @Route(methods={"PUT"}, path="/files/private/{url}")
     */
    public function updateData($url, Request $request, JSONDataService $JSONDataService)
    {
        $checkResult = $this->checkCredentials( $url, $request, $JSONDataService );
        if ($checkResult !== true) {
            return $this->createJsonResponse(false, self::NOT_AVAILIABLE_ERROR);
        }
        $newData = $request->get( 'text' );
        if ( ! $newData) {
            return $this->createJsonResponse( false, self::CLIENT_ERROR );
        }
        $jsonDataObject = $JSONDataService->update( $url, $newData );
        if ( ! $jsonDataObject) {
            return $this->createJsonResponse( false, self::SERVICE_ERROR );
        }
        return $this->createJsonResponse( true );
    }

    private function checkCredentials($url, Request $request, JSONDataService $JSONDataService)
    {
        $jsonDataObject = $JSONDataService->get($url);
        $enteredPassword = $request->server->get('PHP_AUTH_PW');
        if (!$jsonDataObject instanceof JSONSecureData) {
            return false;
        }
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        if (!isset($enteredPassword) || !password_verify($enteredPassword, $jsonDataObject->getPassword())) {
            header( 'HTTP/1.1 401 Authorization Required' );
            header( 'WWW-Authenticate: Basic realm="Access denied"' );
            exit;
        } else {
            return true;
        }
    }
}