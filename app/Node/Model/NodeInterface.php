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

    public function setStartAt(\DateTime $startAt): void;

    public function getLastPing(): ?\DateTime;

    public function setLastPing(\DateTime $lastPing): void;

    public function getLastDown(): ?\DateTime;

    public function setLastDown(\DateTime $lastDown): void;

    public function getStatus(): bool;

    public function setStatus(bool $status): void;

    public function getDowntime(): ?float;

    public function setDowntime(float $downtime): void;
}
