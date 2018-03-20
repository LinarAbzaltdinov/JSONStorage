<?php

namespace App\Repository;

use App\Entity\AbstractData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AbstractDataRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AbstractData::class);
    }

    public function findAll()
    {
        return parent::findBy([], ['createdDate' => 'DESC']);
    }
}