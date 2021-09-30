<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain;

use KejawenLab\ApiSkeleton\Pagination\AliasHelper;
use KejawenLab\ApiSkeleton\Service\AbstractService;
use KejawenLab\ApiSkeleton\Service\Model\ServiceInterface;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Domain\Model\NodeRepositoryInterface;
use KejawenLab\Application\Entity\Service;
use KejawenLab\Application\Repository\ServiceRepository;
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
        MessageBusInterface         $messageBus,
        NodeRepositoryInterface     $repository,
        AliasHelper                 $aliasHelper,
        private ServiceRepository   $serviceRepository,
        private HttpClientInterface $httpClient
    ) {
        parent::__construct($messageBus, $repository, $aliasHelper);
    }

    /**
     * @return NodeInterface[]
     */
    public function getActiveNodes(): array
    {
        return $this->repository->findActiveNodes();
    }

    public function calculateUptime(): float
    {
        return $this->repository->countUptime();
    }

    public function persist(NodeInterface $node): void
    {
        $this->repository->persist($node);
    }

    public function commit(): void
    {
        $this->repository->commit();
    }

    public function totalService(): int
    {
        $total = 0;
        $nodes = $this->all();
        foreach ($nodes as $node) {
            $total += count($this->getServices($node));
        }

        return $total;
    }

    public function ping(NodeInterface $node): bool
    {
        try {
            $response = $this->httpClient->request(Request::METHOD_GET, sprintf('%s/api/ping', $node->getHost()), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Datasama-Key' => $node->getApiKey(),
                ],
            ]);
            $node->setLastPing(new \DateTime());
            if (Response::HTTP_OK === $response->getStatusCode()) {
                if (!$node->getStatus()) {
                    if ($node->getLastDown()) {
                        $node->setDowntime((new \DateTime())->getTimestamp() - $node->getLastDown()->getTimestamp());
                    }
                }

                if (null === $node->getStartAt()) {
                    $node->setStartAt(new \DateTimeImmutable());
                }

                $node->setStatus(true);
            } else {
                if ($node->getStatus()) {
                    $node->setLastDown(new \DateTime());
                }

                $node->setStatus(false);
            }

            $this->save($node);

            $services = $this->getServices($node);
            foreach ($services as $service) {
                $data = $this->serviceRepository->findOneBy(['identifier' => $service['id'], 'type' => $service['type']]);
                if (!$data instanceof Service) {
                    $data = new Service();
                    $data->setNode($node);
                    $data->setIdentifier($service['id']);
                }

                $data->setName($service['id']);
                $data->setType($service['type']);
                $data->setStatus($service['status']);
                $data->setProcessed($service['statistic']['processed']);
                $data->setSuccessed($service['statistic']['successed']);
                $data->setFailed($service['statistic']['failed']);
                $data->setClients($service['statistic']['clients']);

                $this->serviceRepository->persist($data);
            }

            $this->serviceRepository->commit();
        } catch (TransportExceptionInterface) {
            if ($node->getStatus()) {
                $node->setLastDown(new \DateTime());
            }

            $node->setStatus(false);

            $this->save($node);

            return false;
        }

        return true;
    }

    public function getInfo(NodeInterface $node): array
    {
        return $this->request($node, 'info');
    }

    public function getEndpoints(NodeInterface $node): array
    {
        return $this->request($node, 'endpoints');
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

    private function request(NodeInterface $node, string $path): array
    {
        try {
            $response = $this->httpClient->request(Request::METHOD_GET, sprintf('%s/api/%s', $node->getHost(), $path), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Datasama-Key' => $node->getApiKey(),
                ],
            ]);

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                return [];
            }

            $result = json_decode($response->getContent(), true);
            if (array_key_exists('data', $result)) {
                return $result['data'];
            }

            return $result;
        } catch (Throwable) {
            if ($node->getStatus()) {
                $node->setLastDown(new \DateTime());
            }

            $node->setStatus(false);

            $this->save($node);

            return [];
        }
    }
}
