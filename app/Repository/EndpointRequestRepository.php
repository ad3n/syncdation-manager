<?php

declare(strict_types=1);

namespace KejawenLab\Application\Repository;

use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use KejawenLab\ApiSkeleton\ApiClient\Model\ApiClientInterface;
use KejawenLab\ApiSkeleton\Repository\AbstractRepository;
use KejawenLab\Application\Domain\Model\EndpointInterface;
use KejawenLab\Application\Domain\Model\EndpointRequestRepositoryInterface;
use KejawenLab\Application\Entity\EndpointRequest;

/**
 * @method EndpointRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method EndpointRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method EndpointRequest[]    findAll()
 * @method EndpointRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class EndpointRequestRepository extends AbstractRepository implements EndpointRequestRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EndpointRequest::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countEndpointPerApiClientToday(EndpointInterface $endpoint, ApiClientInterface $apiClient)
    {
        $today = new DateTime();
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->select('COUNT(1) as total');
        $queryBuilder->join('o.endpoint', 'e');
        if ($endpoint->isPerClient()) {
            $queryBuilder->join('o.apiClient', 'a');
            $queryBuilder->andWhere($queryBuilder->expr()->eq('a.id', $queryBuilder->expr()->literal($apiClient->getId())));
        }

        $queryBuilder->andWhere($queryBuilder->expr()->eq('e.id', $queryBuilder->expr()->literal($endpoint->getId())));
        $queryBuilder->andWhere($queryBuilder->expr()->gte('o.createdAt', $queryBuilder->expr()->literal($today->format('Y-m-d 00:00:00'))));
        $queryBuilder->andWhere($queryBuilder->expr()->lte('o.createdAt', $queryBuilder->expr()->literal($today->format('Y-m-d 23:59:59'))));

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countEndpointPerApiClientMonth(EndpointInterface $endpoint, ApiClientInterface $apiClient)
    {
        $today = new DateTime();
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->select('COUNT(1) as total');
        $queryBuilder->join('o.endpoint', 'e');
        if ($endpoint->isPerClient()) {
            $queryBuilder->join('o.apiClient', 'a');
            $queryBuilder->andWhere($queryBuilder->expr()->eq('a.id', $queryBuilder->expr()->literal($apiClient->getId())));
        }

        $queryBuilder->andWhere($queryBuilder->expr()->eq('e.id', $queryBuilder->expr()->literal($endpoint->getId())));
        $queryBuilder->andWhere($queryBuilder->expr()->gte('o.createdAt', $queryBuilder->expr()->literal($today->format('Y-m-01 00:00:00'))));
        $queryBuilder->andWhere($queryBuilder->expr()->lte('o.createdAt', $queryBuilder->expr()->literal($today->format('Y-m-t 23:59:59'))));

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
