<?php

declare(strict_types=1);

namespace KejawenLab\Application\Node\Model;

use KejawenLab\ApiSkeleton\Pagination\Model\PaginatableRepositoryInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
interface EndpointRepositoryInterface extends PaginatableRepositoryInterface
{
    public function findByNodeAndPath(NodeInterface $node, string $path): ?EndpointInterface;

    /**
     * @param NodeInterface $node
     *
     * @return EndpointInterface[]
     */
    public function findByNode(NodeInterface $node): array;

    public function countRequest(): int;
}
