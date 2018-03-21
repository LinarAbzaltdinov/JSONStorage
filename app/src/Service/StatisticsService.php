<?php
/**
 * Created by PhpStorm.
 * User: elama
 * Date: 21.03.18
 * Time: 14:36
 */

namespace App\Service;


use App\Entity\JSONData;
use Doctrine\ORM\EntityManager;

class StatisticsService
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getAllStats()
    {
        /** @var JSONData[] $allData */
        $allData            = $this->em->getRepository( JSONData::class )->findAll();
        $allSize            = 0;
        $downloadableAmount = 0;
        $downloadableSize   = 0;
        foreach ($allData as $elem) {
            $curSize = strlen( $elem->getData() );
            $allSize += $curSize;
            if ( ! $elem->isDeleted()) {
                $downloadableAmount++;
                $downloadableSize += $curSize;
            }
        }
        return [
            'allAmount'          => count( $allData ),
            'allSize'            => $allSize,
            'downloadableAmount' => $downloadableAmount,
            'downloadableSize'   => $downloadableSize,
        ];
    }

}