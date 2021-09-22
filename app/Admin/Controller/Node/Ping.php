<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Node;

use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Domain\NodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="NODE", actions={Permission::EDIT})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Ping extends AbstractController
{
    public function __construct(private NodeService $service)
    {
    }

    #[Route(path: '/services/nodes/{id}/ping', name: Ping::class, methods: ['GET'])]
    public function __invoke(Request $request, string $id): Response
    {
        $node = $this->service->get($id);
        if (!$node instanceof NodeInterface) {
            $this->addFlash('error', 'sas.page.node.not_found');

            return new RedirectResponse($this->generateUrl(Main::class, $request->query->all()));
        }

        if ($this->service->ping($node)) {
            $this->addFlash('info', 'sas.page.node.ping_success');
        } else {
            $this->addFlash('error', 'sas.page.node.ping_failed');
        }

        return new RedirectResponse($this->generateUrl(Main::class, $request->query->all()));
    }
}
