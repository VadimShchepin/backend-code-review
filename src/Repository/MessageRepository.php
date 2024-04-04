<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\MessageStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * Finds messages by status.
     *
     * @param string|null $status The status to filter messages by.
     * @return Message[] Returns an array of Message objects filtered by status.
     */
    public function findByStatus(?string $status): array
    {
        $qb = $this->createQueryBuilder('m');

        if ($status && MessageStatusEnum::tryFrom($status)) {
            $qb->andWhere('m.status = :status')
                ->setParameter('status', MessageStatusEnum::tryFrom($status));
        }

        /** @var \App\Entity\Message[] $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
