<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain\Model;

use KejawenLab\ApiSkeleton\Pagination\Model\PaginatableRepositoryInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
interface EndpointRepositoryInterface extends PaginatableRepositoryInterface
{
    public function findByNodeAndPath(NodeInterface $node, string $path): ?EndpointInterface;

    public function findByPath(string $path): array;

    /**
     * @param NodeInterface $node
     *
     * @return EndpointInterface[]
     */
    public function findByNode(NodeInterface $node): array;

    public function countRequest(): int;
}
