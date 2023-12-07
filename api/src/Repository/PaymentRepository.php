<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

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

        if ($customer != 'all') {
            $id = new Uuid($customer);
            $queryBuilder->andWhere('c.id = :customerId')
                ->setParameter('customerId', $id->toBinary());
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

        if ($customer != 'all') {
            $id = new Uuid($customer);
            $queryBuilder->innerJoin(Customer::class, 'c')
                ->andWhere('c.id = :customerId')
                ->setParameter('customerId', $id->toBinary());
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

    public function countDayIncome(): float
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.createdAt >= :startOfDay AND p.createdAt < :endOfDay')
            ->setParameter('startOfDay', new \DateTime('today midnight'))
            ->setParameter('endOfDay', new \DateTime('tomorrow midnight'));

        return (float)$queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function countPaymentsByMethod()
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as paymentsCount, p.paidBy')
            ->groupBy('p.paidBy');

        return $queryBuilder->getQuery()->getResult();
    }

    public function countPaymentsByStatus()
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as paymentsCount, p.status')
            ->groupBy('p.status');

        return $queryBuilder->getQuery()->getResult();
    }

    public function getMonthIncome(): array
    {
        $now = new \DateTime();
        $firstDayOfMonth = new \DateTime($now->format('Y-m-01'));
        $lastDayOfMonth = new \DateTime($now->format('Y-m-t'));

        $sql = "
            SELECT SUM(p.amount) as totalAmount, DATE(p.created_at) as dateWithoutTime
            FROM payment p
            WHERE p.status = 1
            AND p.created_at >= :firstDayOfMonth
            AND p.created_at <= :lastDayOfMonth
            GROUP BY dateWithoutTime
        ";

        $entityManager = $this->getEntityManager();

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('totalAmount', 'totalAmount');
        $rsm->addScalarResult('dateWithoutTime', 'dateWithoutTime');

        $nativeQuery = $entityManager->createNativeQuery($sql, $rsm);
        $nativeQuery->setParameters([
            'firstDayOfMonth' => $firstDayOfMonth->format('Y-m-d'),
            'lastDayOfMonth' => $lastDayOfMonth->format('Y-m-d'),
        ]);

        return $nativeQuery->getResult();
    }
}
