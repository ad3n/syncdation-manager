<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Node;

use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Node\Model\NodeInterface;
use KejawenLab\Application\Node\NodeService;
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
final class Put extends AbstractController
{
    public function __construct(private NodeService $service)
    {
    }

    #[Route(path: '/services/nodes/{id}/edit', name: Put::class, methods: ['GET'])]
    public function __invoke(Request $request, string $id): Response
    {
        $node = $this->service->get($id);
        if (!$node instanceof NodeInterface) {
            $this->addFlash('error', 'sas.page.node.not_found');

            return new RedirectResponse($this->generateUrl(Main::class, $request->query->all()));
        }

        $this->service->ping($node);

        $this->addFlash('id', $node->getId());

        return new RedirectResponse($this->generateUrl(Main::class, $request->query->all()));
    }
}
