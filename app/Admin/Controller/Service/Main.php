<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Service;

use KejawenLab\ApiSkeleton\Admin\Controller\AbstractController;
use KejawenLab\ApiSkeleton\Pagination\Paginator;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\ServiceService;
use KejawenLab\Application\Entity\Service;
use KejawenLab\Application\Form\ServiceType;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="SERVICE", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Main extends AbstractController
{
    public function __construct(private ServiceService $service, Paginator $paginator)
    {
        parent::__construct($this->service, $paginator);
    }

    /**
     * @Route("/services", name=Main::class, methods={"GET", "POST"})
     */
    public function __invoke(Request $request): Response
    {
        $service = new Service();
        if ($request->isMethod(Request::METHOD_POST)) {
            $id = $request->getSession()->get('id');
            if (null !== $id) {
                $service = $this->service->get($id);
            }
        } else {
            $flashes = $request->getSession()->getFlashBag()->get('id');
            foreach ($flashes as $flash) {
                $service = $this->service->get($flash);
                if (null !== $service) {
                    $request->getSession()->set('id', $service->getId());

                    break;
                }
            }
        }

        $form = $this->createForm(ServiceType::class, $service);
        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->service->save($form->getData());
                $this->addFlash('info', 'sas.page.service.saved');

                $form = $this->createForm(ServiceType::class);
            }
        }

        return $this->renderList($form, $request, new ReflectionClass(Service::class));
    }
}
