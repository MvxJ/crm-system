<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 *
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function save(Payment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Payment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countPayments($status, $paidBy, $customer): int
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as payments_count')
            ->innerJoin('p.customer', 'c');

        if ($status != 'all' && !is_nan((int)$status)) {
            $queryBuilder->andWhere('p.status = :status')
                ->setParameter('status', (int)$status);
        }

        if ($paidBy != 'all' && !is_nan((int)$paidBy)) {
            $queryBuilder->andWhere('p.paidBy = :paidBy')
                ->setParameter('paidBy', (int)$paidBy);
        }

        if ($customer != 'all' && !is_nan((int)$customer)) {
            $queryBuilder->andWhere('c.id = :customerId')
                ->setParameter('customerId', (int)$customer);
        }

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

    public function getPaymentsWithPagination(
        $page,
        $itemsPerPage,
        $order,
        $orderBy,
        $status,
        $paidBy,
        $customer
    ): array {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('p.' . $orderBy, $order)
            ->orderBy('p.createdAt', 'desc');

        if ($status != 'all' && !is_nan((int)$status)) {
            $queryBuilder->andWhere('p.status = :status')
                ->setParameter('status', (int)$status);
        }

        if ($paidBy != 'all' && !is_nan((int)$paidBy)) {
            $queryBuilder->andWhere('p.paidBy = :paidBy')
                ->setParameter('paidBy', (int)$paidBy);
        }

        if ($customer != 'all' && !is_nan((int)$customer)) {
            $queryBuilder->innerJoin(Customer::class, 'c')
                ->andWhere('c.id = :customerId')
                ->setParameter('customerId', (int)$customer);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findCustomerPayments(string $customerEmail): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->setMaxResults(24)
            ->orderBy('p.createdAt', 'desc')
            ->innerJoin(Customer::class, 'c')
            ->where('c.email = :email')
            ->setParameter('email', $customerEmail);

        return $queryBuilder->getQuery()->getResult();
    }
}
