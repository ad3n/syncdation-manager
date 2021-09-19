<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Endpoint;

use KejawenLab\ApiSkeleton\Admin\Controller\AbstractController;
use KejawenLab\ApiSkeleton\Pagination\Paginator;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Entity\Endpoint;
use KejawenLab\Application\Form\EndpointType;
use KejawenLab\Application\Node\EndpointService;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Main extends AbstractController
{
    public function __construct(private EndpointService $service, Paginator $paginator)
    {
        parent::__construct($this->service, $paginator);
    }

    #[Route(path: '/services/endpoints', name: Main::class, methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        $endpoint = new Endpoint();
        if ($request->isMethod(Request::METHOD_POST)) {
            $id = $request->getSession()->get('id');
            if (null !== $id) {
                $endpoint = $this->service->get($id);
            }
        } else {
            $flashes = $request->getSession()->getFlashBag()->get('id');
            foreach ($flashes as $flash) {
                $endpoint = $this->service->get($flash);
                if (null !== $endpoint) {
                    $request->getSession()->set('id', $endpoint->getId());

                    break;
                }
            }
        }

        $form = $this->createForm(EndpointType::class, $endpoint);
        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if ('' === $data->getId()) {
                    $status = $this->service->add($form->getData());
                } else {
                    $status = $this->service->update($form->getData());
                }

                if ($status) {
                    $this->addFlash('info', 'sas.page.endpoint.saved');
                } else {
                    $this->addFlash('error', 'sas.page.endpoint.not_saved');
                }

                $form = $this->createForm(EndpointType::class);
            }
        }

        return $this->renderList($form, $request, new ReflectionClass(Endpoint::class));
    }
}
