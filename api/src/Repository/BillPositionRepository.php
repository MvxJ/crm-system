<?php

namespace App\Repository;

use App\Entity\BillPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BillPosition>
 *
 * @method BillPosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method BillPosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method BillPosition[]    findAll()
 * @method BillPosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillPositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BillPosition::class);
    }

    public function save(BillPosition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BillPosition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
