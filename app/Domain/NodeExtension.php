<?php

declare(strict_types=1);

namespace KejawenLab\Application\Domain;

use Iterator;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Repository\ServiceRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class NodeExtension extends AbstractExtension
{
    public function __construct(private ServiceRepository $serviceRepository)
    {
    }

    /**
     * @return Iterator<TwigFunction>
     */
    public function getFunctions(): iterable
    {
        yield new TwigFunction('node_services', [$this, 'getServices']);
    }

    /**
     * @param NodeInterface $node
     *
     * @return NodeInterface[]
     */
    public function getServices(NodeInterface $node): array
    {
        return $this->serviceRepository->findByNode($node);
    }
}
