<?php

declare(strict_types=1);

namespace KejawenLab\Application\Repository;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use KejawenLab\ApiSkeleton\Repository\AbstractRepository;
use KejawenLab\Application\Entity\License;

/**
 * @method License|null find($id, $lockMode = null, $lockVersion = null)
 * @method License|null findOneBy(array $criteria, array $orderBy = null)
 * @method License[]    findAll()
 * @method License[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class LicenseRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, License::class);
    }

    public function findByKey(string $key): ?License
    {
        return $this->findOneBy(['key' => $key]);
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
}
