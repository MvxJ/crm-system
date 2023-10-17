<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\ServiceRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceRequest>
 *
 * @method ServiceRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceRequest[]    findAll()
 * @method ServiceRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceRequest::class);
    }

    public function save(ServiceRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ServiceRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getServiceRequestsWithPagination(
        int $page = 0,
        int $itemsPerPage = 25,
        string $order,
        string $orderBy,
        string $status,
        string $customerId,
        string $userId
    ): array {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('s.' . $orderBy, $order);

        if ($status != 'all' && !is_nan((int)$status)) {
            $queryBuilder->where('s.status = :status')
                ->setParameter('status', (int)$status);
        }

        if ($customerId != 'all' && !is_nan((int)$customerId)) {
            $queryBuilder->innerJoin(Customer::class, 'c')
                ->where('c.id = :customerId')
                ->setParameter('customerId', (int)$customerId);
        }

        if ($userId != 'all' && !is_nan((int)$userId)) {
            $queryBuilder->innerJoin(User::class, 'u')
                ->where('u.id = :userId')
                ->setParameter('userId', (int)$userId);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function countServiceRequests(string $status, string $userId, string $customerId): int
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('COUNT(s.id) as service_request_count');

        if ($status != 'all' && !is_nan((int)$status)) {
            $queryBuilder->where('s.status = :status')
                ->setParameter('status', (int)$status);
        }

        if ($customerId != 'all' && !is_nan((int)$customerId)) {
            $queryBuilder->innerJoin(Customer::class, 'c')
                ->where('c.id = :customerId')
                ->setParameter('customerId', (int)$customerId);
        }

        if ($userId != 'all' && !is_nan((int)$userId)) {
            $queryBuilder->innerJoin(User::class, 'u')
                ->where('u.id = :userId')
                ->setParameter('userId', (int)$userId);
        }

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

    public function findContractsWithPaginationByCustomer(string $userEmail): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->innerJoin(Customer::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $userEmail);

        return $queryBuilder->getQuery()->getResult();
    }

    public function countContractsByCustomer(string $userEmail): int
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('COUNT(s.id) as service_request_count')
            ->innerJoin(Customer::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $userEmail);

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }
}
