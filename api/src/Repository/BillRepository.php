<?php

namespace App\Repository;

use App\Entity\Bill;
use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bill>
 *
 * @method Bill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bill[]    findAll()
 * @method Bill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bill::class);
    }

    public function save(Bill $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Bill $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countBills(string $status): int
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->select('COUNT(b.id) as device_count');

        if ($status != 'all' && !is_nan((int)$status)) {
            $queryBuilder->where('b.status = :status')
                ->setParameter('status', (int)$status);
        }

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

    public function findBillsWithPagination(
        int $page = 0,
        int $itemsPerPage = 25,
        string $order,
        string $orderBy,
        string $status
    ): array {
        $queryBuilder = $this->createQueryBuilder('b');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('b.' . $orderBy, $order);

        if ($status != 'all' && !is_nan((int)$status)) {
            $queryBuilder->where('b.status = :status')
                ->setParameter('status', (int)$status);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function countCustomerBills(string $email): int
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->select('COUNT(b.id) as bills_count')
            ->innerJoin('b.customer', 'c')
            ->where('c.email = :email')
            ->setParameter('email', $email);

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

    public function findCustomerBillsWithPagination(
        int $page = 0,
        int $itemsPerPage = 12,
        string $order,
        string $orderBy,
        string $email
    ): array {
        $queryBuilder = $this->createQueryBuilder('b');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('b.' . $orderBy, $order)
            ->innerJoin(Customer::class, 'c')
            ->where('c.email = :email')
            ->setParameter('email', $email);

        return $queryBuilder->getQuery()->getResult();
    }
}
