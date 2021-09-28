<?php

declare(strict_types=1);

namespace KejawenLab\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use KejawenLab\ApiSkeleton\Entity\EntityInterface;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Repository\ServiceRepository;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 * @ORM\Table(name="app_services")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Service implements EntityInterface
{
    const TYPE_FILE = 'file';
    const TYPE_ELASTICSEARCH = 'elasticsearch';
    const TYPE_DATABASE = 'database';

    use BlameableEntity;
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     * @OA\Property(type="string")
     */
    private ?UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=Node::class, cascade={"persist"})
     *
     * @Groups({"read"})
     **/
    private ?NodeInterface $node;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=17)
     *
     * @Groups({"read"})
     */
    private ?string $type;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"read"})
     */
    private bool $status;

    /**
     * @ORM\Column(type="bigint")
     *
     * @Groups({"read"})
     */
    private int $processed;

    /**
     * @ORM\Column(type="bigint")
     *
     * @Groups({"read"})
     */
    private int $successed;

    /**
     * @ORM\Column(type="bigint")
     *
     * @Groups({"read"})
     */
    private int $failed;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"read"})
     */
    private int $clients;

    public function __construct()
    {
        $this->id = null;
        $this->node = null;
        $this->name = null;
        $this->type = null;
        $this->status = true;
        $this->processed = 0;
        $this->successed = 0;
        $this->failed = 0;
        $this->clients = 0;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getNode(): ?NodeInterface
    {
        return $this->node;
    }

    public function setNode(?NodeInterface $node): void
    {
        $this->node = $node;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        if (!in_array($type, [self::TYPE_DATABASE, self::TYPE_ELASTICSEARCH, self::TYPE_FILE])) {
            throw new \InvalidArgumentException('Invalid type');
        }

        $this->type = $type;
    }

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    public function getProcessed(): int
    {
        return $this->processed;
    }

    public function setProcessed(int $processed): void
    {
        $this->processed = $processed;
    }

    public function getSuccessed(): int
    {
        return $this->successed;
    }

    public function setSuccessed(int $successed): void
    {
        $this->successed = $successed;
    }

    public function getFailed(): int
    {
        return $this->failed;
    }

    public function setFailed(int $failed): void
    {
        $this->failed = $failed;
    }

    public function getClients(): int
    {
        return $this->clients;
    }

    public function setClients(int $clients): void
    {
        $this->clients = $clients;
    }

    public function getNullOrString(): ?string
    {
        return $this->getName();
    }
}
