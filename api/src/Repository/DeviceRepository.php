<?php

namespace App\Repository;

use App\Entity\Device;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Device>
 *
 * @method Device|null find($id, $lockMode = null, $lockVersion = null)
 * @method Device|null findOneBy(array $criteria, array $orderBy = null)
 * @method Device[]    findAll()
 * @method Device[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }
    public function save(Device $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function remove(Device $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findDevicesWithPagination(
        int $page = 0,
        int $itemsPerPage = 25,
        string $orderBy,
        string $order,
        string $status,
    ): array {
        $queryBuilder = $this->createQueryBuilder('d');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('d.' . $orderBy, $order);

            if ($status != 'all' && !is_nan((int)$status)) {
                $queryBuilder->where('d.status = :status')
                    ->setParameter('status', (int)$status);
            }

        return $queryBuilder->getQuery()->getResult();
    }

    public function countDevices(string $status): int
    {
        $queryBuilder = $this->createQueryBuilder('d')
            ->select('COUNT(d.id) as device_count');

        if ($status != 'all' && !is_nan((int)$status)) {
            $queryBuilder->where('d.status = :status')
                ->setParameter('status', (int)$status);
        }

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }
}
