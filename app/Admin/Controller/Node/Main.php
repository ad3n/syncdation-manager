<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Node;

use KejawenLab\ApiSkeleton\Admin\Controller\AbstractController;
use KejawenLab\ApiSkeleton\Pagination\Paginator;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Entity\Node;
use KejawenLab\Application\Form\NodeType;
use KejawenLab\Application\Domain\NodeService;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="NODE", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Main extends AbstractController
{
    public function __construct(private NodeService $service, Paginator $paginator)
    {
        parent::__construct($this->service, $paginator);
    }

    #[Route(path: '/services/nodes', name: Main::class, methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        $node = new Node();
        if ($request->isMethod(Request::METHOD_POST)) {
            $id = $request->getSession()->get('id');
            if (null !== $id) {
                $node = $this->service->get($id);
            }
        } else {
            $flashes = $request->getSession()->getFlashBag()->get('id');
            foreach ($flashes as $flash) {
                $node = $this->service->get($flash);
                if (null !== $node) {
                    $request->getSession()->set('id', $node->getId());

                    break;
                }
            }
        }

        $form = $this->createForm(NodeType::class, $node);
        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->service->save($form->getData());
                $this->addFlash('info', 'sas.page.node.saved');

                $form = $this->createForm(NodeType::class);
            }
        }

        return $this->renderList($form, $request, new ReflectionClass(Node::class));
    }
}
