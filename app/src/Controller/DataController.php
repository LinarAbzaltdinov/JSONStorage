<?php

namespace App\Controller;

use App\Service\JSONDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DataController extends AbstractController
{
    const SERVICE_ERROR = "Ошибка";
    const CLIENT_ERROR = "Данные от клиента не получены";
    const NODATA_ERROR = "Данные не найдены";

    /**
     * @Route(methods={"POST"}, path="/upload")
     */
    public function createData(Request $request, JSONDataService $JSONDataService)
    {
        $inputText = $request->request->get("text");
        $deleteAfterFirstAccess = $request->request->get("deleteAfterAccess");
        if ($deleteAfterFirstAccess === null) {
            $deleteAfterFirstAccess = false;
        }
        if (!$inputText) {
            return $this->errorResponse(self::CLIENT_ERROR);
        }
        $jsonDataObject = $JSONDataService->create($inputText, $deleteAfterFirstAccess);
        if (!$jsonDataObject) {
            return $this->errorResponse(self::SERVICE_ERROR);
        }
        return $this->render('showLink.html.twig',
            [
                'url' => $jsonDataObject->getUrl()
            ]);

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