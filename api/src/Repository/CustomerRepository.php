<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function save(Customer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Customer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getCustomersWithPagination(int $limit = 25, int $page = 1, ?string $searchTerm = null)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->orderBy('c.id', 'ASC');

        if ($searchTerm) {
            $queryBuilder
                ->andWhere(
                     $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like('c.firstName', ':searchTerm'),
                        $queryBuilder->expr()->like('c.lastName', ':searchTerm')
                    )
                )->setParameter('searchTerm', '%' . $searchTerm . '%');
        }    

        return $queryBuilder->getQuery()->getResult();
    }

    public function countActiveCustomers(): int
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.isDisabled = false');

        return (int)$queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function getCustomer2faCount(): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as totalCustomers')
            ->addSelect('SUM(CASE WHEN c.emailAuthEnabled = true THEN 1 ELSE 0 END) as enabled2FA')
            ->addSelect('SUM(CASE WHEN c.emailAuthEnabled = false THEN 1 ELSE 0 END) as disabled2FA')
            ->where('c.isDisabled = false');

        return $queryBuilder->getQuery()->getSingleResult();
    }

    public function getCustomerAccountsConfirmedStatistics()
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as totalCustomers')
            ->addSelect('SUM(CASE WHEN c.authenticated = true THEN 1 ELSE 0 END) as authenticated')
            ->addSelect('SUM(CASE WHEN c.authenticated = false THEN 1 ELSE 0 END) as notAuthenticated')
            ->where('c.isDisabled = false');

        return $queryBuilder->getQuery()->getSingleResult();
    }
}
