<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain\Model;

use KejawenLab\ApiSkeleton\Entity\EntityInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
interface EndpointRequestInterface extends EntityInterface
{
    public function getEndpoint(): ?EndpointInterface;

    public function setEndpoint(EndpointInterface $endpoint): void;

    public function getQueries(): array;

    public function setQueries(array $queries): void;
}
