<?php

namespace App\Repository;

use App\Entity\Contract;
use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contract>
 *
 * @method Contract|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contract|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contract[]    findAll()
 * @method Contract[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contract::class);
    }

    public function save(Contract $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contract $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findContractsWithPagination(
        int $page = 0,
        int $itemsPerPage = 25,
        string $order,
        string $orderBy,
        string $status,
        string $customerId
    ): array {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('c.' . $orderBy, $order);

        if ($status != 'all' && !is_nan((int)$status)) {
            $queryBuilder->where('c.status = :status')
                ->setParameter('status', (int)$status);
        }

        if ($customerId != 'all' && !is_nan((int)$customerId)) {
            $queryBuilder->innerJoin('c.user', 'customer')->where('customer.id = :customerId')
                ->setParameter('customerId', (int)$customerId);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function countContracts(string $status, string $customerId): int
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as device_count');

        if ($status != 'all' && !is_nan((int)$status)) {
            $queryBuilder->where('c.status = :status')
                ->setParameter('status', (int)$status);
        }

        if ($customerId != 'all' && !is_nan((int)$customerId)) {
            $queryBuilder->innerJoin('c.user', 'customer')->where('customer.id = :customerId')
                ->setParameter('customerId', (int)$customerId);
        }

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

    public function getUserContracts(string $userEmail): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->innerJoin(Customer::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $userEmail);

        return $queryBuilder->getQuery()->getResult();
    }
}
