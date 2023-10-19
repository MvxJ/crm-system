<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function save(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countCommentsByServiceRequestId(int $serviceRequestId): int
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as device_count')
            ->innerJoin('c.serviceRequest', 's')
            ->where('s.id = :serviceRequestId')
            ->setParameter('serviceRequestId', $serviceRequestId);

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

    public function getCommentsByServiceRequestId(
        int $serviceRequestId,
        int $page = 0,
        int $itemsPerPage = 25,
        string $order,
        string $orderBy,
    ): array {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->innerJoin('c.serviceRequest', 's')
            ->where('s.id = :serviceRequestId')
            ->setParameter('serviceRequestId', $serviceRequestId)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('c.' . $orderBy, $order);

        return $queryBuilder->getQuery()->getResult();
    }
}
