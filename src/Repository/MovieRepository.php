<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\Genre;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /**
     * FindAllQuery()
     *
     * @return Query
     */
    public function findAllCustom()
    {
        $query = $this->createQueryBuilder('m')
                ->select('m.title')
                ->addSelect('m.id')
                ->addSelect('m.image')
                ->addSelect('m.release_date')
                ->addSelect('m.slug')
                ->orderBy('m.release_date', 'DESC')
                ->getQuery();

        return $query;
    }

    /**
     * FindByCategory()
     *
     * @return Query
     */
    public function FindByCategory($id)
    {
        $query = $this->createQueryBuilder('m')
                ->innerJoin('m.genres', 'g')
                ->select('m.title')
                ->addSelect('m.id')
                ->addSelect('m.image')
                ->addSelect('m.slug')
                ->where('g.id = :id')
                ->setParameter('id', $id)
                ->getQuery();
                
        return $query;
    }

    public function FindByTitle($title, $categoryId = null){

        $query = $this->createQueryBuilder('m')
                ->select('m.title')
                ->addSelect('m.image')
                ->addSelect('m.slug')
                ->where('m.title LIKE :word')
                ->setParameter('word', '%'.$title.'%');

        if($categoryId) {
            $query->innerJoin('m.genres', 'g')
            ->andWhere('g.id = :id')
            ->setParameter('id', $categoryId->getId() );
        }

        $query->getQuery();

        return $query;
    }

    public function FindLastMovie()
    {
        $query = $this->createQueryBuilder('m')
                ->orderBy('m.id', 'DESC')
                ->setMaxResults( 20 )
                ->getQuery()
                ->execute();
                
        return $query;
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
