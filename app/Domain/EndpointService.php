<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain;
use KejawenLab\ApiSkeleton\ApiClient\Model\ApiClientInterface;
use KejawenLab\ApiSkeleton\Pagination\AliasHelper;
use KejawenLab\ApiSkeleton\Service\AbstractService;
use KejawenLab\ApiSkeleton\Service\Model\ServiceInterface;
use KejawenLab\Application\Domain\Model\EndpointInterface;
use KejawenLab\Application\Domain\Model\EndpointRepositoryInterface;
use KejawenLab\Application\Domain\Model\EndpointRequestRepositoryInterface;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Entity\EndpointRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class EndpointService extends AbstractService implements ServiceInterface
{
    public function __construct(
        MessageBusInterface $messageBus,
        EndpointRepositoryInterface $repository,
        AliasHelper $aliasHelper,
        private HttpClientInterface $httpClient,
        private EndpointRequestRepositoryInterface $endpointRequestRepository,
    ) {
        parent::__construct($messageBus, $repository, $aliasHelper);
    }

    public function totalRequest(): int
    {
        return $this->repository->countRequest();
    }

    public function delete(EndpointInterface $endpoint): bool
    {
        $statusCode = $this->request($endpoint, Request::METHOD_DELETE);
        if (Response::HTTP_NO_CONTENT !== $statusCode) {
            return false;
        }

        $this->remove($endpoint);

        return true;
    }

    public function update(EndpointInterface $endpoint): bool
    {
        $statusCode = $this->request($endpoint, Request::METHOD_PUT);
        if (Response::HTTP_OK !== $statusCode) {
            return false;
        }

        $this->save($endpoint);

        return true;
    }

    public function add(EndpointInterface $endpoint): bool
    {
        $statusCode = $this->request($endpoint, Request::METHOD_POST);
        if (Response::HTTP_CREATED !== $statusCode) {
            return false;
        }

        $this->save($endpoint);

        return true;
    }

    public function getByNodeAndPath(NodeInterface $node, string $path): ?EndpointInterface
    {
        return $this->repository->findByNodeAndPath($node, $path);
    }

    /**
     * @param string $path
     *
     * @return EndpointInterface[]
     */
    public function getByPath(string $path): array
    {
        return $this->repository->findByPath($path);
    }

    /**
     * @param NodeInterface $node
     *
     * @return EndpointInterface[]
     */
    public function getByNode(NodeInterface $node): array
    {
        return $this->repository->findByNode($node);
    }

    public function isAllowToRequest(ApiClientInterface $apiClient, EndpointInterface $endpoint): bool
    {
        $callToday = $this->endpointRequestRepository->countEndpointPerApiClientToday($endpoint, $apiClient);
        $callMonth = $this->endpointRequestRepository->countEndpointPerApiClientMonth($endpoint, $apiClient);

        if (0 === $endpoint->getMaxPerDay()) {
            if (0 === $endpoint->getMaxPerMonth()) {
                return true;
            }

            if ($callMonth >= $endpoint->getMaxPerMonth()) {
                return false;
            }

            return true;
        }

        if ($callToday >= $endpoint->getMaxPerDay()) {
            return false;
        }

        if ($callMonth >= $endpoint->getMaxPerMonth()) {
            return false;
        }

        return true;
    }

    public function call(EndpointInterface $endpoint, ApiClientInterface $apiClient, array $queries): array
    {
        try {
            $node = $endpoint->getNode();
            $response = $this->httpClient->request(Request::METHOD_GET, sprintf('%s/api/exposes%s', $node->getHost(), $endpoint->getPath()), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Datasama-Key' => $node->getApiKey(),
                ],
                'query' => $queries,
            ]);

            $endpoint->call();

            $request = new EndpointRequest();
            $request->setApiClient($apiClient);
            $request->setEndpoint($endpoint);
            $request->setQueries($queries);

            $this->endpointRequestRepository->persist($request);
            $this->save($endpoint);
            $this->endpointRequestRepository->commit();

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                return [];
            }

            return json_decode($response->getContent(), true);
        } catch (Throwable) {
            return [];
        }
    }

    private function request(EndpointInterface $endpoint, string $method): int
    {
        try {
            $data = [
                'path' => $endpoint->getPath(),
                'sql' => [
                    'select' => $endpoint->getSelectSql(),
                    'count' => $endpoint->getCountSql(),
                ],
            ];

            if (count($endpoint->getDefaults()) > 0) {
                $data['defaults'] = $endpoint->getDefaults();
            }

            $node = $endpoint->getNode();
            $response = $this->httpClient->request(
                $method,
                sprintf('%s/api/endpoints', $node->getHost()),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Datasama-Key' => $node->getApiKey(),
                    ],
                    'json' => $data,
                ]
            );

            return $response->getStatusCode();
        } catch (TransportExceptionInterface) {
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }
    }
}
