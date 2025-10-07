<?php

namespace App\Repository;

use App\Entity\Plan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plan>
 */
class PlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plan::class);
    }

    //Kako bi sprecili lazy loading i videli sve planove potrebno je napraviti sopstven query

    public function findWithDetails(int $id) {
                return $this->createQueryBuilder('p')
            ->leftJoin('p.workoutPlans', 'wp')
            ->addSelect('wp')   
            ->leftJoin('p.mealPlans', 'mp')
            ->addSelect('mp') 
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
