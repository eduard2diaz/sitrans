<?php

namespace App\Repository;

use App\Entity\Elefante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Elefante|null find($id, $lockMode = null, $lockVersion = null)
 * @method Elefante|null findOneBy(array $criteria, array $orderBy = null)
 * @method Elefante[]    findAll()
 * @method Elefante[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElefanteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Elefante::class);
    }

//    /**
//     * @return Elefante[] Returns an array of Elefante objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Elefante
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
