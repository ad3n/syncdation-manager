<?php

declare(strict_types=1);

namespace KejawenLab\Application\Node\Model;

use KejawenLab\ApiSkeleton\Entity\EntityInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
interface EndpointInterface extends EntityInterface
{
    public function getNode(): ?NodeInterface;

    public function getPath(): ?string;

    public function getSQL(): ?string;

    public function getDefaults(): array;

    public function getTotalCall(): int;

    public function call(): void;
}
