<?php

declare(strict_types=1);

namespace KejawenLab\Application\Repository;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use KejawenLab\ApiSkeleton\Repository\AbstractRepository;
use KejawenLab\ApiSkeleton\Service\Model\ServiceableRepositoryInterface;
use KejawenLab\Application\Entity\Service;
use KejawenLab\Application\Domain\Model\NodeInterface;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class ServiceRepository extends AbstractRepository implements ServiceableRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function commit(): void
    {
        $this->_em->flush();
    }

    public function persist(object $object): void
    {
        $this->_em->persist($object);
    }

    public function countByNode(NodeInterface $node): int
    {
        return count($this->findBy(['node' => $node]));
    }

    public function findByNode(NodeInterface $node): array
    {
        return $this->findBy(['node' => $node]);
    }
}
