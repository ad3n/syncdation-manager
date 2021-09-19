<?php

declare(strict_types=1);

namespace KejawenLab\Application\Node;
use KejawenLab\ApiSkeleton\Pagination\AliasHelper;
use KejawenLab\ApiSkeleton\Service\AbstractService;
use KejawenLab\ApiSkeleton\Service\Model\ServiceInterface;
use KejawenLab\Application\Node\Model\EndpointInterface;
use KejawenLab\Application\Node\Model\EndpointRepositoryInterface;
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
        private HttpClientInterface $httpClient
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

    public function getByPath(string $path): ?EndpointInterface
    {
        return $this->repository->findByPath($path);
    }

    public function call(EndpointInterface $endpoint, array $queries): array
    {
        try {
            $node = $endpoint->getNode();
            $response = $this->httpClient->request(Request::METHOD_GET, sprintf('%s%s/exposes%s', $node->getHost(), $node->getPrefix(), $endpoint->getPath()), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Syncdation-Key' => $node->getApiKey(),
                ],
                'query' => $queries,
            ]);

            $endpoint->call();

            $this->save($endpoint);

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
            $node = $endpoint->getNode();
            $response = $this->httpClient->request(
                $method,
                sprintf('%s%s/endpoints', $node->getHost(), $node->getPrefix()),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Syncdation-Key' => $node->getApiKey(),
                    ],
                    'json' => [
                        'path' => $endpoint->getPath(),
                        'sql' => $endpoint->getSQL(),
                        'defaults' => $endpoint->getDefaults()?: null,
                    ]
                ]
            );

            return $response->getStatusCode();
        } catch (TransportExceptionInterface) {
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }
    }
}
