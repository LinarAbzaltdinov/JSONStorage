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
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Validator\Constraints\DateTime;

class CronJobExecutor
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function runJob($minute, $hour, $day, $month, $year, $sql)
    {
        //changing UTC+3 to UTC
        $date = new \DateTime( "now" , new \DateTimeZone("Europe/Moscow") );
        $date->setDate($year, $month, $day);
        $date->setTime($hour, $minute);
        $date->setTimezone(new \DateTimeZone("UTC"));
        $minute = $date->format('i');
        $hour = $date->format('H');
        $day = $date->format('d');
        $month = $date->format('m');

        $rsm = new ResultSetMapping();
        $this->em->createNativeQuery("SELECT cron.schedule('$minute $hour $day $month *', $$".$sql."$$)", $rsm)
            ->execute();
    }

}