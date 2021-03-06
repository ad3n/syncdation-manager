<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain\Model;

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

    public function getStartAt(): ?\DateTimeImmutable;

    public function setStartAt(\DateTimeImmutable $startAt): void;

    public function getLastPing(): ?\DateTime;

    public function setLastPing(\DateTime $lastPing): void;

    public function getLastDown(): ?\DateTime;

    public function setLastDown(\DateTime $lastDown): void;

    public function getStatus(): bool;

    public function setStatus(bool $status): void;

    public function getDowntime(): ?int;

    public function setDowntime(int $downtime): void;
}
