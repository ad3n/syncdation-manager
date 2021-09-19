<?php

namespace KejawenLab\Application\Command;

use KejawenLab\Application\Node\NodeService;
use Swoole\Coroutine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class NodePingCommand extends Command
{
    public function __construct(private NodeService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('syncdation:node:ping')
            ->setDescription('Ping node')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nodes = $this->service->all();
        foreach ($nodes as $node) {
            Coroutine::create(function () use ($node): void {
                $this->service->ping($node);
            });
        }

        return 0;
    }
}
