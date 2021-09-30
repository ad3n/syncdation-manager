<?php

declare(strict_types=1);

namespace KejawenLab\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use KejawenLab\ApiSkeleton\ApiClient\Model\ApiClientInterface;
use KejawenLab\ApiSkeleton\Entity\ApiClient;
use KejawenLab\Application\Domain\Model\EndpointInterface;
use KejawenLab\Application\Domain\Model\EndpointRequestInterface;
use KejawenLab\Application\Repository\EndpointRequestRepository;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EndpointRequestRepository::class)
 * @ORM\Table(name="app_endpoint_requests")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class EndpointRequest implements EndpointRequestInterface
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
     * @ORM\ManyToOne(targetEntity=ApiClient::class, cascade={"persist"})
     *
     * @Groups({"read"})
     * @MaxDepth(1)
     */
    private ApiClientInterface $apiClient;

    /**
     * @ORM\ManyToOne(targetEntity=Endpoint::class, cascade={"persist"})
     *
     * @Assert\NotBlank()
     *
     * @Groups({"read"})
     **/
    private ?EndpointInterface $endpoint;

    /**
     * @Groups({"read"})
     *
     * @ORM\Column(type="json")
     *
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    private array $queries;

    public function __construct()
    {
        $this->id = null;
        $this->endpoint = null;
        $this->queries = [];
    }

    public function getId(): ?string
    {
        return (string) $this->id;
    }

    public function getApiClient(): ApiClientInterface
    {
        return $this->apiClient;
    }

    public function setApiClient(ApiClientInterface $apiClient): void
    {
        $this->apiClient = $apiClient;
    }

    public function getEndpoint(): ?EndpointInterface
    {
        return $this->endpoint;
    }

    public function setEndpoint(EndpointInterface $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function setQueries(array $queries): void
    {
        $this->queries = $queries;
    }

    public function getNullOrString(): ?string
    {
        return null;
    }
}
