<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain;

use KejawenLab\ApiSkeleton\Pagination\AliasHelper;
use KejawenLab\ApiSkeleton\Service\AbstractService;
use KejawenLab\ApiSkeleton\Service\Model\ServiceInterface;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Entity\License;
use KejawenLab\Application\Entity\Node;
use KejawenLab\Application\Repository\LicenseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class LicenseService extends AbstractService implements ServiceInterface
{
    public function __construct(
        MessageBusInterface $messageBus,
        LicenseRepository $repository,
        AliasHelper $aliasHelper,
        private HttpClientInterface $httpClient,
        private NodeService $nodeService,
        private string $licenseServer,
        private string $licenseServerKey
    ) {
        parent::__construct($messageBus, $repository, $aliasHelper);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function add(string $license): bool
    {
        $response = $this->httpClient->request(
            Request::METHOD_GET,
            sprintf('%s/licenses', $this->licenseServer),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Syncdation-Key' => $this->licenseServerKey,
                    'X-Syncdation-License-Key' => $license,
                ]
            ]
        );

        if ($response->getStatusCode() != Response::HTTP_OK) {
            return false;
        }

        $result = json_decode($response->getContent(), true);
        $holder = $this->repository->findByKey($license);
        if (!$holder instanceof License) {
            $holder = new License();
            $holder->setKey($license);
        }

        $holder->setName($result['name']);
        $holder->setEmail($result['email']);
        $holder->setCompany($result['company']);
        $holder->setIssuedBy($result['issued_by']);
        $holder->setValidUntil($result['valid_until']);
        $holder->setActivatedAt($result['activate_at']);
        $holder->setStatus($result['status']);
        $holder->setMaxNode($result['max_node']);
        $holder->setMaxService($result['max_service']);

        foreach ($result['active_nodes'] as $host => $status) {
            $node = $this->nodeService->getByHost($host);
            if (!$node instanceof NodeInterface) {
                $node = new Node();
                $node->setHost($host);

                $this->nodeService->persist($node);
            }
        }

        $this->save($holder);
        $this->nodeService->commit();

        return true;
    }
}
