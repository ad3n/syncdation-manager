<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Node;

use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Domain\NodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="NODE", actions={Permission::DELETE})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Delete extends AbstractController
{
    public function __construct(private NodeService $service)
    {
    }

    /**
     * @Route("/nodes/{id}/delete", name=Delete::class, methods={"GET"})
     */
    public function __invoke(string $id): Response
    {
        $node = $this->service->get($id);
        if (!$node instanceof NodeInterface) {
            $this->addFlash('error', 'sas.page.node.not_found');

            return new RedirectResponse($this->generateUrl(Main::class));
        }

        $this->service->remove($node);

        $this->addFlash('info', 'sas.page.node.deleted');

        return new RedirectResponse($this->generateUrl(Main::class));
    }
}
