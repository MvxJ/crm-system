<?php

namespace App\Repository;

use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offer>
 *
 * @method Offer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offer[]    findAll()
 * @method Offer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    public function save(Offer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Offer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOffersWithPagination(
        int $limit = 25,
        int $page = 1,
        string $order,
        string $orderBy,
        string $type,
        ?string $searchTerm
    ) {
        $currentDate = new \DateTime();

        $queryBuilder = $this->createQueryBuilder('o')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->orderBy('o.id', 'ASC')
            ->where('o.validDue >= :currentDate')
            ->orWhere('o.validDue IS NULL')
            ->setParameter('currentDate', $currentDate);

        if ($type != 'all' && !is_nan((int)$type)) {
            $queryBuilder->andWhere('o.type = :type')->setParameter('type', (int)$type);
        }

        if ($searchTerm) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('o.title', ':term'))->setParameter('term', '%' . $searchTerm . '%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function countOffers(string $type, ?string $searchTerm): int
    {
        $currentDate = new \DateTime();

        $queryBuilder = $this->createQueryBuilder('o')
            ->select('COUNT(o.id) as document_count')
            ->where('o.validDue >= :currentDate')
            ->orWhere('o.validDue IS NULL')
            ->setParameter('currentDate', $currentDate);

        if ($type != 'all' && !is_nan((int)$type)) {
            $queryBuilder->andWhere('o.type = :type')->setParameter('type', (int)$type);
        }

        if ($searchTerm) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('o.title', ':term'))->setParameter('term', '%' . $searchTerm . '%');
        }

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

    public function countActiveOffers(): int
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.deleted', false);

        return (int)$queryBuilder->getQuery()->getSingleScalarResult();
    }
}
