<?php

namespace App\Repository;

use App\Entity\AbstractData;
use Doctrine\ORM\EntityRepository;

class JSONDataRepository extends EntityRepository
{
    public function findAll()
    {
        return parent::findBy([], ['createdDate' => 'DESC']);
    }

    public function findAllNotDeleted()
    {
        return parent::findBy(['deleted' => 'false'], ['createdDate' => 'DESC']);
    }
}