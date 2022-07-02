<?php

namespace App\Repository;

use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Figure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Figure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Figure[]    findAll()
 * @method Figure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FigureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Figure::class);
    }

    public function getFigureByLimit(int $page, int $limitPerPage)
    {
        $querybuilder = $this->createQueryBuilder('a')
            ->setFirstResult(($page - 1) * $limitPerPage)
            ->setMaxResults($limitPerPage)
            ->orderBy('a.updatedAt', 'DESC');

        return new Paginator($querybuilder);
    }
}
