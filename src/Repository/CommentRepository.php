<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }


    public function getCommentsPagination(int $figureId, int $page, int $limit = 5)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.figure', 'f')
            ->where('f.id = :figureId')
            ->setParameter('figureId', $figureId)
            ->orderBy('c.updatedAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getCommentByLimit(int $page, int $limitPerPage)
    {
        $querybuilder = $this->createQueryBuilder('a')
            ->setFirstResult(($page - 1) * $limitPerPage)
            ->setMaxResults($limitPerPage)
            ->orderBy('a.updatedAt', 'DESC');

        return new Paginator($querybuilder);
    }
}
