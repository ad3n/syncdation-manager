<?php

declare(strict_types=1);

namespace KejawenLab\Application\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use KejawenLab\ApiSkeleton\Repository\AbstractRepository;
use KejawenLab\Application\Domain\Model\EndpointInterface;
use KejawenLab\Application\Domain\Model\EndpointRepositoryInterface;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Entity\Endpoint;

/**
 * @method Endpoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method Endpoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Endpoint[]    findAll()
 * @method Endpoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class EndpointRepository extends AbstractRepository implements EndpointRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Endpoint::class);
    }

    /**
     * @param NodeInterface $node
     *
     * @return EndpointInterface[]
     */
    public function findByNode(NodeInterface $node): array
    {
        return $this->findBy(['node' => $node]);
    }

    public function findByNodeAndPath(NodeInterface $node, string $path): ?EndpointInterface
    {
        return $this->findOneBy(['node' => $node, 'path' => $path]);
    }

    /**
     * @param string $path
     *
     * @return Endpoint[]
     */
    public function findByPath(string $path): array
    {
        return $this->findBy(['path' => $path]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countRequest(): int
    {
        $queryBuilder = $this->queryBuilder('o');
        $queryBuilder->select('SUM(o.totalCall) AS total');

        $query = $queryBuilder->getQuery();

        return $query->getSingleScalarResult();
    }
}
