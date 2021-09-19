<?php

namespace KejawenLab\Application\Command;

use KejawenLab\Application\Entity\Endpoint;
use KejawenLab\Application\Node\EndpointService;
use KejawenLab\Application\Node\NodeService;
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
            Coroutine::create(function () use ($node): void {
                $endpoints = $this->service->getEndpoints($node);
                foreach ($endpoints as $endpoint) {
                    $entity = $this->endpointService->get($endpoint['path']);
                    if (null === $entity) {
                        $entity = new Endpoint();
                        $entity->setPath($endpoint['path']);
                        $entity->setNode($node);
                    }

                    $entity->setSql($endpoint['sql']);
                    $entity->setDefaults($endpoint['defaults']);

                    $this->endpointService->save($entity);
                }
            });
        }

        return 0;
    }
}
