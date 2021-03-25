<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    const LIMIT_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function paginateComments(int $page, int $figureId)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.figure = ' . $figureId)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();
        $page === 1 ? $first = 0 : $first = ($page-1) * self::LIMIT_PER_PAGE;
        $qb->setFirstResult($first)
            ->setMaxResults(self::LIMIT_PER_PAGE);

        return $qb->getResult();
    }

    public function lastPageComments(int $figureId): float|bool
    {
        $qb = $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.figure = ' . $figureId)
            ->getQuery()
            ->getSingleScalarResult();
        return ceil($qb / self::LIMIT_PER_PAGE);
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
