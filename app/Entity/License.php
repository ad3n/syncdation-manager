<?php

declare(strict_types=1);

namespace KejawenLab\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use KejawenLab\ApiSkeleton\Entity\EntityInterface;
use KejawenLab\Application\Repository\LicenseRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LicenseRepository::class)
 * @ORM\Table(name="app_licenses")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @UniqueEntity("key")
 */
class License implements EntityInterface
{
    use BlameableEntity;
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private ?UuidInterface $id;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     */
    private ?string $key;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $company;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $validUntil;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $issuedBy;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $status;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $activatedAt;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private int $maxNode;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private int $maxService;

    public function __construct()
    {
        $this->id = null;
        $this->key = null;
        $this->name = null;
        $this->email = null;
        $this->company = null;
        $this->validUntil = null;
        $this->issuedBy = null;
        $this->status = false;
        $this->activatedAt = null;
        $this->maxNode = 0;
        $this->maxService = 0;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): void
    {
        $this->key = $key;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    public function getValidUntil(): ?string
    {
        return $this->validUntil;
    }

    public function setValidUntil(string $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    public function getIssuedBy(): ?string
    {
        return $this->issuedBy;
    }

    public function setIssuedBy(?string $issuedBy): void
    {
        $this->issuedBy = $issuedBy;
    }

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    public function getActivatedAt(): ?string
    {
        return $this->activatedAt;
    }

    public function setActivatedAt(string $activatedAt): void
    {
        $this->activatedAt = $activatedAt;
    }

    public function getMaxNode(): int
    {
        return $this->maxNode;
    }

    public function setMaxNode(int $maxNode): void
    {
        $this->maxNode = $maxNode;
    }

    public function getMaxService(): int
    {
        return $this->maxService;
    }

    public function setMaxService(int $maxService): void
    {
        $this->maxService = $maxService;
    }

    public function getNullOrString(): ?string
    {
        return $this->getName();
    }
}
