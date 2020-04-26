<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Permet d'obtenir la tâche qui précède le z_order donnée en paramètre
     * 
     * @param int $listId
     * @param int $zOrder
     * 
     * @return Task
     */
    public function getPreviousTask($listId, $zOrder)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.task_list = :list_id')
            ->andWhere('t.z_order < :z_order')
            ->setParameter('list_id', $listId)
            ->setParameter('z_order', $zOrder)
            ->orderBy('t.z_order', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        ;
    }

    /**
     * Permet d'obtenir la tâche qui suit le z_order donnée en paramètre
     * 
     * @param int $listId
     * @param int $zOrder
     * 
     * @return Task
     */
    public function getNextTask(int $listId, int $zOrder)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.task_list = :list_id')
            ->andWhere('t.z_order > :z_order')
            ->setParameter('list_id', $listId)
            ->setParameter('z_order', $zOrder)
            ->orderBy('t.z_order', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        ;
    }
}
