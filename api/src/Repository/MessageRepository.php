<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function save(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countMessages(string $customerId, string $type): int
    {
        $queryBuilder = $this->createQueryBuilder('m')
            ->select('COUNT(m.id) as payments_count')
            ->innerJoin('m.customer', 'c');

        if ($customerId != 'all') {
            $id = new Uuid($customerId);
            $queryBuilder->andWhere('c.id = :id')
                ->setParameter('id', $id->toBinary());
        }

        if ($type != 'all' && !is_nan((int)$type)) {
            $queryBuilder->andWhere('m.type = :type')
                ->setParameter('type', (int)$type);
        }


        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

    public function getMessagesWithPagination(
        int $page = 1,
        int $itemsPerPage = 25,
        string $order,
        string $orderBy,
        string $customerId,
        string $type
    ): array {
        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('m.' . $orderBy, $order);

        if ($type != 'all' && !is_nan((int)$type)) {
            $queryBuilder->andWhere('m.type = :type')
                ->setParameter('type', (int)$type);
        }

        if ($customerId != 'all') {
            $id = new Uuid($customerId);
            $queryBuilder->innerJoin('m.customer', 'c')
                ->andWhere('c.id = :customerId')
                ->setParameter('customerId', $id->toBinary());
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function countCustomerMessages(string $userEmail): int
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as payments_count')
            ->innerJoin('p.customer', 'c')
            ->where('c.email = :email')
            ->setParameter('email', $userEmail);


        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        return (int)$result;
    }

    public function getCustomerMessages(
        int $page = 1,
        int $itemsPerPage = 25,
        string $order,
        string $orderBy,
        string $userEMail
    ): array {
        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->orderBy('m.' . $orderBy, $order)
            ->innerJoin(Customer::class, 'c')
            ->where('c.email = :email')
            ->setParameter('email', $userEMail);

        return $queryBuilder->getQuery()->getResult();
    }

    public function countMessagesByDateAndType(): array
    {
        $sql = "
            SELECT COUNT(m.id) as messageCount, m.type, DATE(m.created_date) as formattedDate
            FROM message m
            GROUP BY formattedDate, m.type
        ";

        $entityManager = $this->getEntityManager();

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('messageCount', 'messageCount');
        $rsm->addScalarResult('type', 'type');
        $rsm->addScalarResult('formattedDate', 'formattedDate');

        $nativeQuery = $entityManager->createNativeQuery($sql, $rsm);
        $result = $nativeQuery->getResult();

        return $result;
    }
}
