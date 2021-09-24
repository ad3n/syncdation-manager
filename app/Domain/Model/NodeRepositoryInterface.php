<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain\Model;

use KejawenLab\ApiSkeleton\Pagination\Model\PaginatableRepositoryInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
interface NodeRepositoryInterface extends PaginatableRepositoryInterface
{
    public function countUptime(): float;

    public function findByHost(string $host): ?NodeInterface;
}
