<?php

namespace App\Repository;

use App\Entity\TaskList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskList[]    findAll()
 * @method TaskList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskList::class);
    }

    /**
     * Permet d'obtenir la liste qui précède le z_order donnée en paramètre
     * 
     * @return TaskList
     */
    public function getPreviousList($zOrder)
    {
        return $this->createQueryBuilder('tl')
            ->andWhere('tl.z_order < :z_order')
            ->setParameter('z_order', $zOrder)
            ->orderBy('tl.z_order', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        ;
    }

    /**
     * Permet d'obtenir la tâche qui précède le z_order donnée en paramètre
     * 
     * @param int $zOrder
     * 
     * @return TaskList
     */
    public function getNextList($zOrder)
    {
        return $this->createQueryBuilder('tl')
            ->andWhere('tl.z_order > :z_order')
            ->setParameter('z_order', $zOrder)
            ->orderBy('tl.z_order', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        ;
    }

    // /**
    //  * @return TaskList[] Returns an array of TaskList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TaskList
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
