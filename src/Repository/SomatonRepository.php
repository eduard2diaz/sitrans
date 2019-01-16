<?php

namespace App\Repository;

use App\Entity\Somaton;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Somaton|null find($id, $lockMode = null, $lockVersion = null)
 * @method Somaton|null findOneBy(array $criteria, array $orderBy = null)
 * @method Somaton[]    findAll()
 * @method Somaton[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SomatonRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Somaton::class);
    }

//    /**
//     * @return Somaton[] Returns an array of Somaton objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Somaton
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
