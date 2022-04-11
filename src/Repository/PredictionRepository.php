<?php

namespace App\Repository;

use App\Entity\Competition;
use App\Entity\Prediction;
use App\Entity\RoundMatch;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Prediction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prediction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prediction[]    findAll()
 * @method Prediction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PredictionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prediction::class);
    }

    public function findPrediction(User $user, int $matchId): ?Prediction
    {
        return $this->createQueryBuilder('p')
            ->innerJoin(RoundMatch::class, 'rm', 'WITH', 'p.match=rm.id')
            ->where('p.user = :user')
            ->andWhere('rm.matchId = :matchId')
            ->setParameter('user', $user->getId())
            ->setParameter('matchId', $matchId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Prediction[] Returns an array of Prediction objects
     */
    public function findPredictions(RoundMatch $match, Competition $competition, bool $finished = false)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin(RoundMatch::class, 'rm', 'WITH', 'p.match=rm.id')
            ->innerJoin(Competition::class, 'c', 'WITH', 'p.competition=c.id')
            ->where('rm.matchId = :matchId')
            ->andWhere('c.competition = :competition')
            ->andWhere('p.finished = :finished')
            ->andWhere('p.points IS NULL')
            ->setParameter('matchId', $match->getMatchId())
            ->setParameter('competition', $competition->getId())
            ->setParameter('finished', $finished)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Prediction[] Returns an array of Prediction objects
    //  */
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
    public function findOneBySomeField($value): ?Prediction
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
