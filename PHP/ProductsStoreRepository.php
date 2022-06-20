<?php

namespace App\Repository;

use App\Entity\ProductsStore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductsStore>
 *
 * @method ProductsStore|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductsStore|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductsStore[]    findAll()
 * @method ProductsStore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsStoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductsStore::class);
    }

    public function add(ProductsStore $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProductsStore $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAvailable(): array
    {
        return $this->createQueryBuilder('p')
                    ->andWhere('p.quantity > 0')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return [] Returns an array of categories
     */
    public function getCategories(): array
    {
        return $this->createQueryBuilder('p')
                    ->select('p.category')
                    ->distinct()
                    ->getQuery()
                    ->getSingleColumnResult();
    }


    /**
     * @return [] Returns an array of ingredients
     */
    public function getIngredients(): array
    {
        return $this->createQueryBuilder('p')
                    ->where("p.category = 'Artykuły spożywcze'")
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return [] Returns an array of Products
     */
    public function findByIds(array $ids): array
    {   
        return $this->createQueryBuilder('p')
                    ->where('p.id IN (:ids)')
                    ->setParameter('ids', array_values($ids))
                    ->getQuery()
                    ->getArrayResult();
    }





//    /**
//     * @return ProductsStore[] Returns an array of ProductsStore objects
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

//    public function findOneBySomeField($value): ?ProductsStore
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
