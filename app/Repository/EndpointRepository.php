<?php

declare(strict_types=1);

namespace KejawenLab\Application\Repository;

use Doctrine\Persistence\ManagerRegistry;
use KejawenLab\ApiSkeleton\Repository\AbstractRepository;
use KejawenLab\Application\Entity\Endpoint;
use KejawenLab\Application\Node\Model\EndpointInterface;
use KejawenLab\Application\Node\Model\EndpointRepositoryInterface;

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

    public function findByPath(string $path): ?EndpointInterface
    {
        return $this->findOneBy(['path' => $path]);
    }
}
