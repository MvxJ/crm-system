<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\CustomerAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomerAddress>
 *
 * @method CustomerAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerAddress[]    findAll()
 * @method CustomerAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerAddress::class);
    }

    public function save(CustomerAddress $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CustomerAddress $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAddressesByCustomerId(string $customerId): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->innerJoin(Customer::class, 'c');

        if ($customerId != 'all' && !is_nan((int)$customerId)) {
            $queryBuilder->where('c.id = :id')
                ->setParameter('id', (int)$customerId);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findCustomerAddresses(string $userEmail): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->innerJoin(Customer::class, 'c')
            ->where('c.email = :email')
            ->setParameter('email', $userEmail);

        return $queryBuilder->getQuery()->getResult();
    }
}
