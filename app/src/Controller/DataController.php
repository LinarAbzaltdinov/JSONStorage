<?php

namespace App\Controller;

use App\Entity\AbstractData;
use App\Service\JSONValidator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataController extends AbstractController
{
    /**
     * @Route(methods={"GET"}, path="/files")
     */
    public function getAllData()
    {
        $allData = $this->getDoctrine()->getRepository( AbstractData::class )->findAll();
        return $this->render("list.html.twig", ['data' => $allData]);
    }

    /**
     * @Route(methods={"GET"}, path="/files/{url}")
     */
    public function getData( $url )
    {
        $result = $this->getDoctrine()->getRepository( AbstractData::class )
            ->findOneBy( [ 'url' => $url ] );

        if ($result && !$result->isDeleted()) {
            $result->incrementDownloadAmount();
            $this->getDoctrine()->getManager()->flush();
            return $this->render("showData.html.twig",
                ["json" => json_encode(json_decode($result->getData()), JSON_PRETTY_PRINT),
                 "filename" => $result->getId() . '.json',
                 "url" => $result->getUrl()]);
        } else {
            throw $this->createNotFoundException('Data does not exist');
        }
    }

    /**
     * @Route(methods={"DELETE"}, path="/files/{url}")
     */
    public function deleteData( $url )
    {
        $result = $this->getDoctrine()->getRepository( AbstractData::class )
            ->findOneBy( [ 'url' => $url ] );

        if ($result && !$result->isDeleted()) {
            $result->delete();
            $this->getDoctrine()->getManager()->flush();
            return new Response('{"status":true}');
        } else {
            throw $this->createNotFoundException('Data does not exist');
        }
    }

    /**
     * @Route(methods={"PUT"}, path="/files/{url}")
     */
    public function updateData( $url, Request $request, JSONValidator $jsonValidator)
    {
        $result = $this->getDoctrine()->getRepository( AbstractData::class )
            ->findOneBy( [ 'url' => $url ] );

        // !!!! NOT WORKIN, CANT RECIEVE POST
        
        $newData = $request->get('data');
        $json = $jsonValidator->tryParse($newData);
        if (!$json) {
            throw $this->createNotFoundException('Not valid data!');
        }
        if ($result && !$result->isDeleted()) {
            $result->setData($json);
            $this->getDoctrine()->getManager()->flush();
            return new Response('{"status":true}');
        } else {
            throw $this->createNotFoundException('Data does not exist');
        }
    }
}