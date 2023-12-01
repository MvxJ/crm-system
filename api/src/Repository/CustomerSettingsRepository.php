<?php

namespace App\Repository;

use App\Entity\CustomerSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomerSettings>
 *
 * @method CustomerSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerSettings[]    findAll()
 * @method CustomerSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerSettings::class);
    }

    public function save(CustomerSettings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CustomerSettings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countCustomerNotificationsSettingsVariations(): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as total')
            ->addSelect('SUM(CASE WHEN c.emailNotifications = true AND c.smsNotifications = true THEN 1 ELSE 0 END) as bothNotifications')
            ->addSelect('SUM(CASE WHEN c.emailNotifications = true AND c.smsNotifications = false THEN 1 ELSE 0 END) as emailOnly')
            ->addSelect('SUM(CASE WHEN c.emailNotifications = false AND c.smsNotifications = true THEN 1 ELSE 0 END) as smsONly')
            ->addSelect('SUM(CASE WHEN c.emailNotifications = false AND c.smsNotifications = false THEN 1 ELSE 0 END) as noneNotifications');

        return $queryBuilder->getQuery()->getResult();
    }
}
