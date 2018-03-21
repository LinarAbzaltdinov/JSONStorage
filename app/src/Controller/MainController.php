<?php

namespace App\Controller;

use App\Entity\JSONData;
use App\Service\StatisticsService;
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
     * @Route(methods={"GET"}, path="/files")
     */
    public function showAllData()
    {
        $allData = $this->getDoctrine()->getRepository( JSONData::class )->findAllNotDeleted();
        return $this->render( "list.html.twig", [ 'data' => $allData ] );
    }

    /**
     * @Route(methods={"GET"}, path="/stats")
     */
    public function showStats( StatisticsService $statService )
    {
        $stats = $statService->getAllStats();
        return $this->render( "stats.html.twig", $stats );
    }
}