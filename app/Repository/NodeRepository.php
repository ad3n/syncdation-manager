<?php

declare(strict_types=1);

namespace KejawenLab\Application\Repository;

use Doctrine\Persistence\ManagerRegistry;
use KejawenLab\ApiSkeleton\Repository\AbstractRepository;
use KejawenLab\Application\Entity\Node;
use KejawenLab\Application\Domain\Model\NodeRepositoryInterface;

/**
 * @method Node|null find($id, $lockMode = null, $lockVersion = null)
 * @method Node|null findOneBy(array $criteria, array $orderBy = null)
 * @method Node[]    findAll()
 * @method Node[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class NodeRepository extends AbstractRepository implements NodeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Node::class);
    }

    public function countUptime(): float
    {
        $today = new \DateTime();

        $nodes = $this->findAll();
        $uptime = 0;
        $downtime = 0;
        foreach ($nodes as $node) {
            if (null === $node->getStartAt()) {
                continue;
            }

            $uptime += $today->getTimestamp() - $node->getStartAt()->getTimestamp();
            $downtime += $node->getDowntime();
        }

        if (0 === $uptime) {
            return 0;
        }

        return (($uptime - $downtime) / $uptime) * 100;
    }
}
