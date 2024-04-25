<?php
namespace App\Repository;

use App\Entity\Produits;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class ProduitsRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Produits::class);
        $this->paginator = $paginator;
    }

    public function findByFilters(?string $taille, ?int $category, int $page, int $limit): PaginationInterface
    {
        $query = $this->createQueryBuilder('p');

        if ($taille) {
            $query->andWhere('p.Taille = :taille')
                ->setParameter('taille', $taille);
        }

        if ($category) {
            $query->join('p.categorie', 'c')
                ->andWhere('c.id = :category')
                ->setParameter('category', $category);
        }

        $query->orderBy('p.id', 'ASC');

        return $this->paginator->paginate($query, $page, $limit);
    }



//     * @return Produits[] Returns an array of Produits objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produits
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    // src/Repository/ProduitsRepository.php

// src/Repository/ProduitsRepository.php


}
