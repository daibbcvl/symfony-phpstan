<?php


namespace App\MongoRepository;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class ProductRepository extends DocumentRepository
{
    public function findAllOrderedByName()
    {
        return $this->createQueryBuilder()
            ->sort('name', 'ASC')
            ->getQuery()
            ->execute();
    }
}