<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function getMovieWithRelations(int $id)
    {
        // Le createQueryBuilder à l'intérieur du Repository
        // considére qu'on veut forcément faire une requête à partir de la table de Movie
        // On n'a pas donc pas besoin de préciser le from() ni le select()
        $qb = $this->createQueryBuilder('m');

        $qb
            ->addSelect('g, e, p')
            ->leftJoin('m.genres', 'g') // leftJoin pour être sûr d'avoir un résultat même si le movie n'a pas de genres
            ->leftJoin('m.employments', 'e') // leftJoin pour être sûr d'avoir un résutlat même si le movie n'a pas de castings
            ->leftJoin('e.person', 'p') // ici, left join ou inner join, ça n'a pas d'inportance car un Casting a toujours une relation avec une Person
            ->where('m.id = :id')

            ->setParameter('id', $id)
        ;
        // dd($qb->getQuery());

        return $qb->getQuery()->getOneOrNullResult();
    }

    // /**
    //  * @return Movie[] Returns an array of Movie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Movie
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
