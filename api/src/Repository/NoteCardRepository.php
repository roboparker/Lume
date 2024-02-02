<?php

namespace App\Repository;

use App\Entity\NoteCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NoteCard>
 *
 * @method NoteCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method NoteCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method NoteCard[]    findAll()
 * @method NoteCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NoteCard::class);
    }

//    /**
//     * @return NoteCard[] Returns an array of NoteCard objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NoteCard
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
