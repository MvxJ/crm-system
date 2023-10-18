<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\ServiceVisit;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceVisit>
 *
 * @method ServiceVisit|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceVisit|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceVisit[]    findAll()
 * @method ServiceVisit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceVisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceVisit::class);
    }

    public function save(ServiceVisit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ServiceVisit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getServiceVisitsWithPagination(
        int $page = 0,
        int $itemsPerPage = 25,
        string $order,
        string $orderBy,
        string $userId,
        string $customerId
    ): array {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('s.' . $orderBy, $order);

        if ($customerId != 'all' && !is_nan((int)$customerId)) {
            $queryBuilder->innerJoin(Customer::class, 'c')
                ->andWhere('c.id = :customerId')
                ->setParameter('customerId', (int)$customerId);
        }

        if ($userId != 'all' && !is_nan((int)$userId)) {
            $queryBuilder->innerJoin(User::class, 'u')
                ->andWhere('u.id = :userId')
                ->setParameter('userId', (int)$userId);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function countServiceVisits(string $userId, string $customerId): int
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('COUNT(s.id) as service_request_count');

        if ($customerId != 'all' && !is_nan((int)$customerId)) {
            $queryBuilder->innerJoin(Customer::class, 'c')
                ->andWhere('c.id = :customerId')
                ->setParameter('customerId', (int)$customerId);
        }

        if ($userId != 'all' && !is_nan((int)$userId)) {
            $queryBuilder->innerJoin(User::class, 'u')
                ->andWhere('u.id = :userId')
                ->setParameter('userId', (int)$userId);
        }

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

    public function findServiceVisitsByCustomer(string $userEmail): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->innerJoin('s.customer', 'c')
            ->where('c.email = :email')
            ->setParameter('email', $userEmail);

        return $queryBuilder->getQuery()->getResult();
    }

    public function countServiceVisitsByCustomer(string $userEmail): int
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('COUNT(s.id) as service_visits_count')
            ->innerJoin('s.customer', 'c')
            ->where('c.email = :email')
            ->setParameter('email', $userEmail);

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }
}
