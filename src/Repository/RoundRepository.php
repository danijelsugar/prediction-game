<?php

namespace App\Repository;

use App\Entity\Competition;
use App\Entity\Round;
use App\Entity\RoundMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Round|null find($id, $lockMode = null, $lockVersion = null)
 * @method Round|null findOneBy(array $criteria, array $orderBy = null)
 * @method Round[]    findAll()
 * @method Round[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoundRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Round::class);
    }

    /**
     * @return Round[]
     */
    public function findCompetitionRounds(int $competition): array
    {
        return $this->createQueryBuilder('r')
            ->innerJoin(Competition::class, 'c', 'WITH', 'r.competition=c.id')
            ->innerJoin(RoundMatch::class, 'rm', 'WITH', 'r.id=rm.round')
            ->andWhere('c.competition = :competition')
            ->groupBy('r.name, r.id')
            ->orderBy('r.id', 'ASC')
            ->setParameter('competition', $competition)
            ->getQuery()
            ->getResult();
    }

    public function roundCount(): int
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Round[] Returns an array of Round objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Round
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
