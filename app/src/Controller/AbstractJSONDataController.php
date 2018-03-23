<?php

namespace App\Controller;

use App\Entity\JSONSecureData;
use App\Service\CronJobExecutor;
use App\Service\JSONDataService;
use App\Service\PasswordGenerator;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractJSONDataController extends AbstractController
{
    const SERVICE_ERROR = "Ошибка";
    const CLIENT_ERROR = "Данные от клиента не получены";
    const NODATA_ERROR = "Данные не найдены или удалены";
    const PASS_LENGTH = 8;

    /**
     * @Route(methods={"POST"}, path="/upload")
     */
    public function createData(Request $request, JSONDataService $JSONDataService, CronJobExecutor $cronJobExecutor)
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

        $isDeleteByDateTime =
            $request->request->get("deleteByDateTime") === null
                ? false
                : true;
        if ($isDeleteByDateTime) {
            $minute = $request->request->get("minute");
            $hour = $request->request->get("hour");
            $day = $request->request->get("day");
            $month = $request->request->get("month");
            $year = $request->request->get("year");
            if ($minute && $hour && $day && $month) {
                $cronJobExecutor->runJob( $minute, $hour, $day, $month, $year, "UPDATE jsondata SET deleted=TRUE WHERE id="
                                                                        .$jsonDataObject->getId());
            }
        }


        return $this->render('showLink.html.twig', $parameters);
    }

    abstract public function getData($url, Request $request, JSONDataService $JSONDataService);

    abstract public function deleteData($url, Request $request, JSONDataService $JSONDataService);

    abstract public function updateData($url, Request $request, JSONDataService $JSONDataService);

    protected function createJsonResponse($status, $errorMessage = null)
    {
        return new JsonResponse([
            "status" => $status,
            "errorMessage" => $errorMessage
        ]);
    }

    protected function errorResponse($errorMessage)
    {
        return $this->render("error.html.twig",
            [
                "errorMessage" => $errorMessage
            ]);
    }
}