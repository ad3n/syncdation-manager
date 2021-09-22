<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Endpoint;

use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Admin\Controller\Node\Main as NodeMain;
use KejawenLab\Application\Domain\EndpointService;
use KejawenLab\Application\Domain\Model\EndpointInterface;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Domain\NodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::DELETE})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Delete extends AbstractController
{
    public function __construct(private EndpointService $service, private NodeService $nodeService)
    {
    }

    #[Route(path: '/services/nodes/{nodeId}/endpoints/{id}/delete', name: Delete::class, methods: ['GET'])]
    public function __invoke(Request $request, string $nodeId, string $id): Response
    {
        /** @var NodeInterface $node */
        $node = $this->nodeService->get($nodeId);
        if (null === $node) {
            $this->addFlash('error', 'sas.page.node.not_found');

            return new RedirectResponse($this->generateUrl(NodeMain::class));
        }

        $endpoint = $this->service->get($id);
        if (!$endpoint instanceof EndpointInterface) {
            $this->addFlash('error', 'sas.page.endpoint.not_found');

            return new RedirectResponse($this->generateUrl(Main::class, array_merge($request->query->all(), ['nodeId' => $nodeId])));
        }

        $this->service->delete($endpoint);

        $this->addFlash('info', 'sas.page.endpoint.deleted');

        return new RedirectResponse($this->generateUrl(Main::class, array_merge($request->query->all(), ['nodeId' => $nodeId])));
    }
}
