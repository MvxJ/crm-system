<?php

namespace App\Repository;

use App\Entity\Model;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Model>
 *
 * @method Model|null find($id, $lockMode = null, $lockVersion = null)
 * @method Model|null findOneBy(array $criteria, array $orderBy = null)
 * @method Model[]    findAll()
 * @method Model[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    public function save(Model $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Model $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getModelsWithPagination(
        int $page = 0,
        int $itemsPerPage = 25,
        string $orderBy,
        string $order,
        string $type
    ): array {
        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('m.' . $orderBy, $order);

        if ($type != 'all' && !is_nan((int)$type)) {
            $queryBuilder->where('m.type = :type')
                ->setParameter('type', (int)$type);
        }

         return $queryBuilder->getQuery()->getResult();
    }

    public function countModels(string $type): int
    {
        $queryBuilder = $this->createQueryBuilder('m')
            ->select('COUNT(m.id) as model_count');

        if ($type != 'all' && !is_nan((int)$type)) {
            $queryBuilder->where('m.type = :type')
                ->setParameter('type', (int)$type);
        }

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int) $result;
    }
}
