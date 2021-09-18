<?php

declare(strict_types=1);

namespace KejawenLab\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use KejawenLab\ApiSkeleton\Util\StringUtil;
use KejawenLab\Application\Node\Model\NodeInterface;
use KejawenLab\Application\Repository\NodeRepository;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=NodeRepository::class)
 * @ORM\Table(name="app_nodes")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @UniqueEntity(fields={"host"})
 */
class Node implements NodeInterface
{
    use BlameableEntity;
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     * @Groups({"read"})
     *
     * @OA\Property(type="string")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="string", length=7)
     *
     * @Assert\Length(max=7)
     * @Assert\NotBlank()
     *
     * @Groups({"read"})
     */
    private ?string $code;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     *
     * @Groups({"read"})
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     * @Assert\Url()
     *
     * @Groups({"read"})
     */
    private ?string $host;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     *
     * @Groups({"read"})
     */
    private ?string $apiKey;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     *
     * @Groups({"read"})
     */
    private ?\DateTime $startAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Groups({"read"})
     */
    private ?\DateTime $lastPing;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Groups({"read"})
     */
    private ?\DateTime $lastDown;

    /**
     * @ORM\Column(type="float")
     *
     * @Groups({"read"})
     */
    private float $downtime;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"read"})
     */
    private bool $status;

    public function __construct()
    {
        $this->code = null;
        $this->name = null;
        $this->host = null;
        $this->startAt = null;
        $this->lastPing = null;
        $this->lastDowntime = null;
        $this->downtime = 0.0;
        $this->status = false;
    }

    public function getId(): ?string
    {
        return (string) $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = StringUtil::uppercase($code);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = StringUtil::title($name);
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getStartAt(): ?\DateTime
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTime $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getLastPing(): ?\DateTime
    {
        return $this->lastPing;
    }

    public function setLastPing(\DateTime $lastPing): void
    {
        $this->lastPing = $lastPing;
    }

    public function getLastDown(): ?\DateTime
    {
        return $this->lastDown;
    }

    public function setLastDown(?\DateTime $lastDown): void
    {
        $this->lastDown = $lastDown;
    }

    public function getDowntime(): float
    {
        return $this->downtime;
    }

    public function setDowntime(float $downtime): void
    {
        $this->downtime = $downtime;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    public function getNullOrString(): ?string
    {
        return $this->getName();
    }
}
