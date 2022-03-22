<?php

namespace App\Repository;

use App\Entity\Competition;
use App\Entity\Round;
use App\Entity\RoundMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RoundMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoundMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoundMatch[]    findAll()
 * @method RoundMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoundMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoundMatch::class);
    }

    public function findRoundMatches(int $competition, $round)
    {
        return $this->createQueryBuilder('rm')
            ->select('rm.id, r.name AS round, c.competition, rm.matchId, rm.date, rm.homeTeamName, rm.awayTeamName, rm.fullTimeHomeTeamScore, rm.fullTimeAwayTeamScore, rm.extraTimeHomeTeamScore, rm.extraTimeAwayTeamScore, rm.winner')
            ->innerJoin(Round::class, 'r', 'WITH', 'rm.round=r.id')
            ->innerJoin(Competition::class, 'c', 'WITH', 'r.competition=c.id')
            ->where('c.competition = :competition')
            ->andWhere('r.name = :round')
            ->setParameter('competition', $competition)
            ->setParameter('round', $round)
            ->orderBy('rm.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return RoundMatch[] Returns an array of RoundMatch objects
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
    public function findOneBySomeField($value): ?RoundMatch
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
