<?php

namespace App\Repository;

use App\Entity\Pruebalitro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Pruebalitro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pruebalitro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pruebalitro[]    findAll()
 * @method Pruebalitro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PruebalitroRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pruebalitro::class);
    }

//    /**
//     * @return Pruebalitro[] Returns an array of Pruebalitro objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pruebalitro
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
