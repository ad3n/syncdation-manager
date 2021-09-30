<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain\Model;

use KejawenLab\ApiSkeleton\Entity\EntityInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
interface EndpointInterface extends EntityInterface
{
    public function getNode(): ?NodeInterface;

    public function getPath(): ?string;

    public function getSelectSql(): ?string;

    public function getCountSql(): ?string;

    public function getDefaults(): array;

    public function getMaxPerDay(): int;

    public function getMaxPerMonth(): int;

    public function isPerClient(): bool;

    public function getTotalCall(): int;

    public function call(): void;
}
