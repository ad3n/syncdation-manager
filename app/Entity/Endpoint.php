<?php

declare(strict_types=1);

namespace KejawenLab\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use KejawenLab\ApiSkeleton\Util\StringUtil;
use KejawenLab\Application\Domain\Model\EndpointInterface;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Repository\EndpointRepository;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EndpointRepository::class)
 * @ORM\Table(name="app_endpoints")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @UniqueEntity(fields={"path"})
 */
class Endpoint implements EndpointInterface
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
    private ?UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=Node::class, cascade={"persist"})
     *
     * @Assert\NotBlank()
     *
     * @Groups({"read"})
     **/
    private ?NodeInterface $node;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     *
     * @Groups({"read"})
     */
    private ?string $path;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     *
     * @Groups({"read"})
     */
    private ?string $selectSql;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     *
     * @Groups({"read"})
     */
    private ?string $countSql;

    /**
     * @ORM\Column(type="json")
     *
     * @Groups({"read"})
     *
     * @OA\Property(type="string[]")
     */
    private array $defaults;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"read"})
     */
    private int $maxPerDay;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"read"})
     */
    private int $maxPerMonth;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"read"})
     */
    private bool $perClient;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"read"})
     */
    private int $totalCall;

    public function __construct()
    {
        $this->id = null;
        $this->node = null;
        $this->path = null;
        $this->selectSql = null;
        $this->countSql = null;
        $this->defaults = [];
        $this->perClient = false;
        $this->maxPerDay = 0;
        $this->maxPerMonth = 0;
        $this->totalCall = 0;
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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = StringUtil::lowercase($path);
    }

    public function getSelectSql(): ?string
    {
        return $this->selectSql;
    }

    public function setSelectSql(?string $selectSql): void
    {
        $this->selectSql = $selectSql;
    }

    public function getCountSql(): ?string
    {
        return $this->countSql;
    }

    public function setCountSql(?string $countSql): void
    {
        $this->countSql = $countSql;
    }

    public function getDefaults(): array
    {
        return $this->defaults;
    }

    public function setDefaults(array $defaults): void
    {
        $this->defaults = $defaults;
    }

    public function getMaxPerDay(): int
    {
        return $this->maxPerDay;
    }

    public function setMaxPerDay(int $maxPerDay): void
    {
        $this->maxPerDay = $maxPerDay;
    }

    public function getMaxPerMonth(): int
    {
        return $this->maxPerMonth;
    }

    public function setMaxPerMonth(int $maxPerMonth): void
    {
        $this->maxPerMonth = $maxPerMonth;
    }

    public function isPerClient(): bool
    {
        return $this->perClient;
    }

    public function setPerClient(bool $perClient): void
    {
        $this->perClient = $perClient;
    }

    public function getTotalCall(): int
    {
        return $this->totalCall;
    }

    public function call(): void
    {
        $this->totalCall++;
    }

    public function getNullOrString(): ?string
    {
        return $this->getPath();
    }
}
