<?php

namespace App\Controller;

use App\Entity\JSONData;
use App\Service\JSONValidator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{
    /**
     * @Route(methods={"GET"}, path="/")
     * @return Response
     */
    public function index()
    {
        return $this->render( "upload.html.twig" );
    }

    /**
     * @Route(methods={"POST"}, path="/upload")
     */
    public function upload(Request $request, JSONValidator $jsonValidator)
    {
        $inputText = $request->request->get("text");
        $json = $jsonValidator->tryParse($inputText);
        if ($json) {
            $jsonData = new JSONData($inputText);
            $em = $this->getDoctrine()->getManager();
            $em->persist($jsonData);
            $em->flush();
            return new Response($jsonData->getData());
        }
        return new Response("FAIL");
    }

}