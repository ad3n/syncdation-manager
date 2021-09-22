<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain;

use KejawenLab\ApiSkeleton\Pagination\AliasHelper;
use KejawenLab\ApiSkeleton\Service\AbstractService;
use KejawenLab\ApiSkeleton\Service\Model\ServiceInterface;
use KejawenLab\Application\Domain\Model\EndpointRepositoryInterface;
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
    private const LICENSE_SERVER = 'http://192.168.8.102:1579';
    private const LICENSE_SERVER_KEY = '807955a4461f3742dc9715389bf0b488c17bbbc55888638fe5768ceea0ac00520701def30411b530a1124e00ad429014623f49ce15901996c1f1ae3ffc2079cf';

    public function __construct(
        MessageBusInterface $messageBus,
        LicenseRepository $repository,
        AliasHelper $aliasHelper,
        private HttpClientInterface $httpClient,
        private NodeService $nodeService
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
            sprintf('%s/licenses', self::LICENSE_SERVER),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Syncdation-Key' => self::LICENSE_SERVER_KEY,
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
            $node = new Node();
            $node->setHost($host);

            $this->nodeService->persist($node);
        }

        $this->save($holder);
        $this->nodeService->commit();

        return true;
    }
}
