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
    const TYPE_EXCEL = 'excel';

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
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private ?string $hosts;

    /**
     * @ORM\Column(type="string", length=27)
     *
     * @Groups({"read"})
     */
    private ?string $index;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private ?string $directory;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private ?string $driver;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private ?string $dbHost;

    /**
     * @ORM\Column(type="smallint")
     *
     * @Groups({"read"})
     */
    private int $dbPort;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private ?string $dbUser;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private ?string $dbPassword;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private ?string $dbName;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private ?string $dbTable;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private ?string $dbColumns;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"read"})
     */
    private ?string $prefixName;

    /**
     * @ORM\Column(type="string", length=17)
     *
     * @Groups({"read"})
     */
    private ?string $rotation;

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
        $this->hosts = null;
        $this->index = null;
        $this->directory = null;
        $this->driver = null;
        $this->dbHost = null;
        $this->dbPort = 0;
        $this->dbUser = null;
        $this->dbPassword = null;
        $this->dbName = null;
        $this->dbTable = null;
        $this->dbColumns = null;
        $this->prefixName = null;
        $this->rotation = null;
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
        if (!in_array($type, [self::TYPE_DATABASE, self::TYPE_ELASTICSEARCH, self::TYPE_EXCEL, self::TYPE_FILE])) {
            throw new \InvalidArgumentException('Invalid type');
        }

        $this->type = $type;
    }

    public function getHosts(): ?string
    {
        return $this->hosts;
    }

    public function setHosts(string $hosts): void
    {
        $this->hosts = $hosts;
    }

    public function getIndex(): ?string
    {
        return $this->index;
    }

    public function setIndex(string $index): void
    {
        $this->index = $index;
    }

    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    public function getDriver(): ?string
    {
        return $this->driver;
    }

    public function setDriver(string $driver): void
    {
        $this->driver = $driver;
    }

    public function getDbHost(): ?string
    {
        return $this->dbHost;
    }

    public function setDbHost(string $dbHost): void
    {
        $this->dbHost = $dbHost;
    }

    public function getDbPort(): int
    {
        return $this->dbPort;
    }

    public function setDbPort(int $dbPort): void
    {
        $this->dbPort = $dbPort;
    }

    public function getDbUser(): ?string
    {
        return $this->dbUser;
    }

    public function setDbUser(string $dbUser): void
    {
        $this->dbUser = $dbUser;
    }

    public function getDbPassword(): ?string
    {
        return $this->dbPassword;
    }

    public function setDbPassword(?string $dbPassword): void
    {
        $this->dbPassword = $dbPassword;
    }

    public function getDbName(): ?string
    {
        return $this->dbName;
    }

    public function setDbName(string $dbName): void
    {
        $this->dbName = $dbName;
    }

    public function getDbTable(): ?string
    {
        return $this->dbTable;
    }

    public function setDbTable(string $dbTable): void
    {
        $this->dbTable = $dbTable;
    }

    public function getDbColumns(): ?string
    {
        return $this->dbColumns;
    }

    public function setDbColumns(string $dbColumns): void
    {
        $this->dbColumns = $dbColumns;
    }

    public function getPrefixName(): ?string
    {
        return $this->prefixName;
    }

    public function setPrefixName(string $prefixName): void
    {
        $this->prefixName = $prefixName;
    }

    public function getRotation(): ?string
    {
        return $this->rotation;
    }

    public function setRotation(string $rotation): void
    {
        $this->rotation = $rotation;
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
