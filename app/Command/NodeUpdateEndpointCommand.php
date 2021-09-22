<?php

namespace KejawenLab\Application\Command;

use KejawenLab\Application\Entity\Endpoint;
use KejawenLab\Application\Domain\EndpointService;
use KejawenLab\Application\Domain\NodeService;
use Swoole\Coroutine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class NodeUpdateEndpointCommand extends Command
{
    public function __construct(private NodeService $service, private EndpointService $endpointService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('syncdation:node:endpoint-update')
            ->setDescription('Update endpoint')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nodes = $this->service->all();
        foreach ($nodes as $node) {
            $endpoints = $this->endpointService->getByNode($node);
            foreach ($endpoints as $endpoint) {
                Coroutine::create(function () use ($node, $endpoint): void {
                    $serverEndpoints = $this->service->getEndpoints($node);
                    $exist = false;
                    foreach ($serverEndpoints as $serverEndpoint) {
                        if ($endpoint->getPath() === $serverEndpoint['path']) {
                            $exist = true;
                        }

                        $entity = $this->endpointService->getByNodeAndPath($node, $serverEndpoint['path']);
                        if (null === $entity) {
                            $entity = new Endpoint();
                            $entity->setPath($serverEndpoint['path']);
                            $entity->setNode($node);
                        }

                        $entity->setSql($serverEndpoint['sql']);
                        $entity->setDefaults($serverEndpoint['defaults']);

                        $this->endpointService->save($entity);
                    }

                    if (!$exist) {
                        $this->endpointService->add($endpoint);
                    }
                });
            }
        }

        return 0;
    }
}
