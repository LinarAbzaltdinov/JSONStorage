<?php

namespace App\Controller;

use App\Service\JSONDataService;
use App\Service\JSONValidator;
use App\Service\XMLConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class XMLConverterController extends AbstractController
{
    /**
     * @Route(methods={"POST"}, path="/xml")
     */
    public function convertToXML(Request $request, JSONValidator $JSONValidator, XMLConverter $XMLConverter)
    {
        $inputText = $request->get('text');
        $jsonString = $JSONValidator->tryParse($inputText);
        if (!$jsonString) {
            return $this->render("error.html.twig",
                [
                    "errorMessage" => "Ошибка в JSON"
                ]);
        }
        $xmlData = $XMLConverter->convertToXML($jsonString);
        if (!$xmlData) {
            return $this->render("error.html.twig",
                [
                    "errorMessage" => "Ошибка при конвертировании"
                ]);
        }
        return $this->render("showXml.html.twig",
            [
                "data" => $xmlData,
                "filename" => 'temp.xml'
            ]);
    }
}