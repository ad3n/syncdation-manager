<?php

declare(strict_types=1);

namespace KejawenLab\Application\Node\Model;

use KejawenLab\ApiSkeleton\Entity\EntityInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
interface NodeInterface extends EntityInterface
{
    public function getCode(): ?string;

    public function getName(): ?string;

    public function getHost(): ?string;

    public function getApiKey(): ?string;

    public function getStartAt(): ?\DateTime;

    public function getLastPing(): ?\DateTime;

    public function getStatus(): bool;

    public function getUptime(): ?float;

    public function getDowntime(): ?float;
}
