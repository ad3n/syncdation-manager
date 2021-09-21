<?php

declare(strict_types=1);

namespace KejawenLab\Application\Node;

use Iterator;
use KejawenLab\Application\Node\Model\NodeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class NodeExtension extends AbstractExtension
{
    public function __construct(private NodeService $service)
    {
    }

    /**
     * @return Iterator<TwigFunction>
     */
    public function getFunctions(): iterable
    {
        yield new TwigFunction('node_info', [$this, 'getInfo']);
        yield new TwigFunction('node_services', [$this, 'getServices']);
    }

    public function getInfo(NodeInterface $node): array
    {
        $info = $this->service->getInfo($node);
        if (array_key_exists('holder', $info)) {
            return $info['holder'];
        }

        return [];
    }

    public function getServices(NodeInterface $node): array
    {
        return $this->service->getServices($node);
    }
}
