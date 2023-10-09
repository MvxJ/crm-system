<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Document>
 *
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function save(Document $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Document $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getDocumentsWithPagination(
        int $page,
        int $itemsPerPage,
        string $order,
        string $orderBy,
        bool $isClient
    ): array {
        $queryBuilder = $this->createQueryBuilder('d');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('d.' . $orderBy, $order);

        if ($isClient) {
            $queryBuilder->where('d.isForClients = :forClients')
                ->setParameter('forClients', true);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function countDocuments(bool $isForClient): int
    {
        $queryBuilder = $this->createQueryBuilder('d')
            ->select('COUNT(d.id) as document_count');

        if ($isForClient) {
            $queryBuilder->where('d.isForClients = :forClients')
                ->setParameter('forClients', true);
        }

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

//    /**
//     * @return Document[] Returns an array of Document objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Document
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
