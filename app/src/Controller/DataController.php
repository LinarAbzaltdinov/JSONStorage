<?php

namespace App\Controller;

use App\Entity\JSONSecureData;
use App\Service\JSONDataService;
use App\Service\PasswordGenerator;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DataController extends AbstractController
{
    const SERVICE_ERROR = "Ошибка";
    const CLIENT_ERROR = "Данные от клиента не получены";
    const NODATA_ERROR = "Данные не найдены";
    const PASS_LENGTH = 8;

    /**
     * @Route(methods={"POST"}, path="/upload")
     */
    public function createData(Request $request, JSONDataService $JSONDataService)
    {
        $inputText = $request->request->get("text");
        $deleteAfterFirstAccess =
            $request->request->get("deleteAfterAccess") === null
                ? false
                : true;
        $isSecure =
            $request->request->get("isSecure") === null
                ? false
                : true;
        if (!$inputText) {
            return $this->errorResponse(self::CLIENT_ERROR);
        }

        $jsonDataObject = $JSONDataService->create($inputText, $deleteAfterFirstAccess, $isSecure);
        if (!$jsonDataObject) {
            return $this->errorResponse(self::SERVICE_ERROR);
        }
        $parameters = ['url' => $jsonDataObject->getUrl()];
        if ($jsonDataObject instanceof JSONSecureData) {
            $pass = PasswordGenerator::generate(self::PASS_LENGTH);
            $isPassSet = $JSONDataService->setPasswordToSecureData($jsonDataObject, $pass);
            if (!$isPassSet) {
                return $this->errorResponse(self::SERVICE_ERROR);
            }
            $parameters['password'] = $pass;
        }
        return $this->render('showLink.html.twig', $parameters);
    }

    /**
     * @Route(methods={"GET"}, path="/files/{url}")
     */
    public function getData($url, JSONDataService $JSONDataService)
    {
        $jsonDataObject = $JSONDataService->get($url);
        if (!$jsonDataObject) {
            return $this->errorResponse(self::NODATA_ERROR);
        }
        if ($jsonDataObject instanceof JSONSecureData) {
            return $this->forward('App\Controller\DataController::getSecureData', ['$url' => $url]);
        }
        return $this->render("showJson.html.twig",
            [
                "data" => $jsonDataObject->getPrettyPrintData(),
                "filename" => $jsonDataObject->getId() . '.json',
                "url" => $jsonDataObject->getUrl()
            ]);
    }

    /**
     * @Route(methods={"DELETE"}, path="/files/{url}")
     */
    public function deleteData($url, JSONDataService $JSONDataService)
    {
        $jsonDataObject = $JSONDataService->delete($url);
        if (!$jsonDataObject) {
            return $this->createJsonResponse(false, self::SERVICE_ERROR);
        }
        return $this->createJsonResponse(true);
    }

    /**
     * @Route(methods={"PUT"}, path="/files/{url}")
     */
    public function updateData($url, Request $request, JSONDataService $JSONDataService)
    {
        $newData = $request->get('text');
        if (!$newData) {
            return $this->createJsonResponse(false, self::CLIENT_ERROR);
        }
        $jsonDataObject = $JSONDataService->update($url, $newData);
        if (!$jsonDataObject) {
            return $this->createJsonResponse(false, self::SERVICE_ERROR);
        }
        return $this->createJsonResponse(true);
    }


    /**
     * @Route(methods={"GET"}, path="/files/private/{url}")
     */
    public function getSecureData($url, Request $request, JSONDataService $JSONDataService)
    {
        $enteredPassword = $request->server->get('PHP_AUTH_PW');
        $jsonData = $JSONDataService->get($url);
        if (!$jsonData instanceof JSONSecureData) {
            return $this->redirect("/files/$url");
        }
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        if (!isset($enteredPassword) || !password_verify($enteredPassword, $jsonData->getPassword())) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            exit;
        } else {
            return $this->forward( 'App\Controller\DataController::getData',
                [
                    'url'             => $url,
                    'JSONDataService' => $JSONDataService
                ] );
        }
    }







    private function createJsonResponse($status, $errorMessage = null)
    {
        return new JsonResponse([
            "status" => $status,
            "errorMessage" => $errorMessage
        ]);
    }

    private function errorResponse($errorMessage)
    {
        return $this->render("error.html.twig",
            [
                "errorMessage" => $errorMessage
            ]);
    }
}