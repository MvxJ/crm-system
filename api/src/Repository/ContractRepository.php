<?php

namespace App\Repository;

use App\Entity\Contract;
use App\Entity\Customer;
use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

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
            $queryBuilder->andWhere('c.status = :status')
                ->setParameter('status', (int)$status);
        }

        if ($customerId != 'all') {
            $id = new Uuid($customerId);
            $queryBuilder->innerJoin('c.user', 'customer')
                ->andWhere('customer.id = :customerId')
                ->setParameter('customerId', $id->toBinary());
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

        if ($customerId != 'all') {
            $id = new Uuid($customerId);
            $queryBuilder->innerJoin('c.user', 'customer')->where('customer.id = :customerId')
                ->setParameter('customerId', $id->toBinary());
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

    public function countActiveContracts(): int
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.status = :status')
            ->setParameter('status', Contract::CONTRACT_STATUS_ACTIVE);

        return (int)$queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function getContractsCountByType(): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as contractCount, c.type')
            ->groupBy('c.type');

        return $queryBuilder->getQuery()->getResult();
    }

    public function getContractsCountByStatus(): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as contractCount, c.status')
            ->groupBy('c.status');

        return $queryBuilder->getQuery()->getResult();
    }

    public function getAvgInternetSpeed(): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('AVG(o.uploadSpeed) as avgUploadSpeed, AVG(o.downloadSpeed) as avgDownloadSpeed')
            ->leftJoin('c.offer', 'o')
            ->where('o.type IN (:offerTypes)')
            ->andWhere('c.status != :statusClosed')
            ->setParameter('offerTypes', [Offer::TYPE_INTERNET, Offer::TYPE_INTERNET_AND_TELEVISION])
            ->setParameter('statusClosed', Contract::CONTRACT_STATUS_INACTIVE);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getAvgNumberOfCanals(): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('AVG(o.numberOfCanals) as avgNumberOfCanals')
            ->leftJoin('c.offer', 'o')
            ->where('o.type IN (:offerTypes)')
            ->andWhere('c.status != :statusClosed')
            ->setParameter('offerTypes', [Offer::TYPE_TELEVISION, Offer::TYPE_INTERNET_AND_TELEVISION])
            ->setParameter('statusClosed', Contract::CONTRACT_STATUS_INACTIVE);

        return $queryBuilder->getQuery()->getResult();
    }
}
