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

class JSONDataController extends AbstractJSONDataController
{

    /**
     * @Route(methods={"GET"}, path="/files/{url}")
     */
    public function getData($url, Request $request, JSONDataService $JSONDataService)
    {
        $jsonDataObject = $JSONDataService->get($url);
        if (!$jsonDataObject) {
            return $this->errorResponse(self::NODATA_ERROR);
        }
        if ($jsonDataObject instanceof JSONSecureData) {
            return $this->forward('App\Controller\SecureDataController::getData', ['url' => $url]);
        }
        $JSONDataService->incrementDownloadAmount($jsonDataObject);
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
    public function deleteData($url, Request $request, JSONDataService $JSONDataService)
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
}