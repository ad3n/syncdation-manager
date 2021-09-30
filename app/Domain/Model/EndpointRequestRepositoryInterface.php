<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain\Model;

use KejawenLab\ApiSkeleton\ApiClient\Model\ApiClientInterface;
use KejawenLab\ApiSkeleton\Pagination\Model\PaginatableRepositoryInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
interface EndpointRequestRepositoryInterface extends PaginatableRepositoryInterface
{
    public function countEndpointPerApiClientToday(EndpointInterface $endpoint, ApiClientInterface $apiClient);

    public function countEndpointPerApiClientMonth(EndpointInterface $endpoint, ApiClientInterface $apiClient);
}
