<?php

declare(strict_types=1);

namespace KejawenLab\Application\Node;

use KejawenLab\Application\Node\Model\NodeInterface;
use KejawenLab\Application\Node\Model\NodeRepositoryInterface;
use KejawenLab\ApiSkeleton\Pagination\AliasHelper;
use KejawenLab\ApiSkeleton\Service\AbstractService;
use KejawenLab\ApiSkeleton\Service\Model\ServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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

    public function ping(NodeInterface $node)
    {
        $response = $this->httpClient->request(Request::METHOD_GET, sprintf('%s/ping', $this->getApiRoot($node->getHost())));
        $node->setLastPing(new \DateTime());
        if ($response->getStatusCode() === Response::HTTP_OK) {
            if (!$node->getStatus()) {
                $node->setDowntime((float)(new \DateTime())->getTimestamp() - $node->getLastDown()->getTimestamp());
            }

            $node->setStatus(true);
        } else {
            if ($node->getStatus()) {
                $node->setLastDown(new \DateTime());
            }

            $node->setStatus(false);
        }

        $this->save($node);
    }

    public function getEndpoints(NodeInterface $node)
    {
        $response = $this->httpClient->request(Request::METHOD_GET, sprintf('%s/endpoints', $this->getApiRoot($node->getHost())));
    }

    public function addEndpoint()
    {

    }

    public function getServices()
    {
    }

    public function getServiceByType(string $type)
    {

    }

    public function getClients(string $serviceId)
    {

    }

    public function getStatistic(string $serviceId)
    {

    }

    public function getFiles(string $serviceId)
    {

    }

    public function Call(string $path)
    {

    }

    private function getApiRoot(string $host): string
    {
        return sprintf('%s/api', $host);
    }
}
