<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\ServiceRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

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
        string $userId,
        string $customerId
    ): array {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('s.' . $orderBy, $order);

        if ($status != 'all' && !is_nan((int)$status)) {
            $queryBuilder
                ->andWhere('s.status = :status')
                ->setParameter('status', (int)$status);
        }

        if ($customerId != 'all') {
            $id = new Uuid($customerId);
            $queryBuilder->innerJoin('s.customer', 'c')
                ->andWhere('c.id = :customerId')
                ->setParameter('customerId', $id->toBinary());
        }

        if ($userId != 'all') {
            $id = new Uuid($userId);
            $queryBuilder->innerJoin('s.user', 'u')
                ->andWhere('u.id = :userId')
                ->setParameter('userId', $id->toBinary());
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function countServiceRequests(string $status, string $userId, string $customerId): int
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('COUNT(s.id) as service_request_count');

        if ($status != 'all' && !is_nan((int)$status)) {
            $queryBuilder->andWhere('s.status = :status')
                ->setParameter('status', (int)$status);
        }

        if ($customerId != 'all') {
            $id = new Uuid($customerId);
            $queryBuilder->innerJoin('s.customer', 'c')
                ->andWhere('c.id = :customerId')
                ->setParameter('customerId', $id->toBinary());
        }

        if ($userId != 'all') {
            $id = new Uuid($userId);
            $queryBuilder->innerJoin('s.user', 'u')
                ->andWhere('u.id = :userId')
                ->setParameter('userId', $id->toBinary());
        }

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

    public function findServiceRequestsWithPaginationByCustomer(string $userEmail): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->innerJoin('s.customer', 'c')
            ->where('c.email = :email')
            ->setParameter('email', $userEmail);

        return $queryBuilder->getQuery()->getResult();
    }

    public function countServiceRequestsByCustomer(string $userEmail): int
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('COUNT(s.id) as service_request_count')
            ->innerJoin('s.customer', 'c')
            ->where('c.email = :email')
            ->setParameter('email', $userEmail);

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

    public function countNotFinishedServiceRequests(): int
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.status = :statusOpened OR s.status = :statusRealization')
            ->setParameter('statusOpened', ServiceRequest::STATUS_OPENED)
            ->setParameter('statusRealization', ServiceRequest::STATUS_REALIZATION);

        return (int)$queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function countServiceRequestsByOfferType(): array
    {
        $queryBuilder = $this->createQueryBuilder('sr')
            ->select('COUNT(sr.id) as requestCount, o.type as offerType')
            ->leftJoin('sr.contract', 'c')
            ->leftJoin('c.offer', 'o')
            ->groupBy('o.type');

        return $queryBuilder->getQuery()->getResult();
    }
}
