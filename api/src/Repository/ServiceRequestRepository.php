<?php

namespace App\Repository;

use App\Entity\ServiceRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function getServiceRequestsWithPagination()
    {
    }

    public function countServiceRequests(
    ) {

    }

    public function getCustomerServiceRequests()
    {
    }
}
