<?php

declare(strict_types=1);

namespace KejawenLab\Application\Node;

use KejawenLab\ApiSkeleton\Pagination\AliasHelper;
use KejawenLab\ApiSkeleton\Service\AbstractService;
use KejawenLab\ApiSkeleton\Service\Model\ServiceInterface;
use KejawenLab\Application\Node\Model\EndpointInterface;
use KejawenLab\Application\Node\Model\NodeInterface;
use KejawenLab\Application\Node\Model\NodeRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class NodeService extends AbstractService implements ServiceInterface
{
    public function __construct(
        MessageBusInterface $messageBus,
        NodeRepositoryInterface $repository,
        AliasHelper $aliasHelper,
        private HttpClientInterface $httpClient
    ) {
        parent::__construct($messageBus, $repository, $aliasHelper);
    }

    public function ping(NodeInterface $node): void
    {
        try {
            $response = $this->httpClient->request(Request::METHOD_GET, sprintf('%s%s/ping', $node->getHost(), $node->getPrefix()), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Syncdation-Key' => $node->getApiKey(),
                ],
            ]);
            $node->setLastPing(new \DateTime());
            if (Response::HTTP_OK === $response->getStatusCode()) {
                if (!$node->getStatus()) {
                    if ($node->getLastDown()) {
                        $node->setDowntime((float)(new \DateTime())->getTimestamp() - $node->getLastDown()->getTimestamp());
                    }
                }

                $node->setStatus(true);
            } else {
                if ($node->getStatus()) {
                    $node->setLastDown(new \DateTime());
                }

                $node->setStatus(false);
            }

            $this->save($node);
        } catch (TransportExceptionInterface) {
        }
    }

    public function getEndpoints(NodeInterface $node): array
    {
        return $this->request($node, 'endpoints');
    }

    public function addEndpoint(NodeInterface $node, string $path, string $sqlQuery): int
    {
        try {
            $response = $this->httpClient->request(
                Request::METHOD_POST,
                sprintf('%s%s/endpoints', $node->getHost(), $node->getPrefix()),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Syncdation-Key' => $node->getApiKey(),
                    ],
                    'json' => [
                        'path' => $path,
                        'sql' => $sqlQuery,
                    ]
                ]
            );

            return $response->getStatusCode();
        } catch (TransportExceptionInterface) {
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }
    }

    public function getServices(NodeInterface $node): array
    {
        return $this->request($node, 'services');
    }

    public function getServiceByType(NodeInterface $node, string $type): array
    {
        return $this->request($node, sprintf('services/type/%s', $type));
    }

    public function getClients(NodeInterface $node, string $serviceId): array
    {
        return $this->request($node, sprintf('services/%s/clients', $serviceId));
    }

    public function getStatistic(NodeInterface $node, string $serviceId): array
    {
        return $this->request($node, sprintf('services/%s/statistic', $serviceId));
    }

    public function getFiles(NodeInterface $node, string $serviceId): array
    {
        return $this->request($node, sprintf('%s/directories', $serviceId));
    }

    public function call(EndpointInterface $endpoint): array
    {
        try {
            $node = $endpoint->getNode();
            $response = $this->httpClient->request(Request::METHOD_GET, sprintf('%s/%s', $node->getHost(), $endpoint->getEndpoint()), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Syncdation-Key' => $node->getApiKey(),
                ],
            ]);

            $endpoint->call();

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                return [];
            }

            return json_decode($response->getContent(), true);
        } catch (Throwable) {
            return [];
        }
    }

    private function request(NodeInterface $node, string $path): array
    {
        try {
            $response = $this->httpClient->request(Request::METHOD_GET, sprintf('%s%s/%s', $node->getHost(), $node->getPrefix(), $path), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Syncdation-Key' => $node->getApiKey(),
                ],
            ]);

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                return [];
            }

            return json_decode($response->getContent(), true);
        } catch (Throwable) {
            return [];
        }
    }
}
