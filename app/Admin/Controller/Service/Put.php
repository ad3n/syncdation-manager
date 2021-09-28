<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Service;

use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\Model\ServiceInterface;
use KejawenLab\Application\Domain\ServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="SERVICE", actions={Permission::EDIT})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
*/
final class Put extends AbstractController
{
    public function __construct(private ServiceService $service)
    {
    }

    /**
    * @Route("/services/{id}/edit", name=Put::class, methods={"GET"}, priority=1)
    */
    public function __invoke(Request $request, string $id): Response
    {
        $service = $this->service->get($id);
        if (!$service instanceof ServiceInterface) {
            $this->addFlash('error', 'sas.page.service.not_found');

            return new RedirectResponse($this->generateUrl(Main::class, $request->query->all()));
        }

        $this->addFlash('id', $service->getId());

        return new RedirectResponse($this->generateUrl(Main::class, $request->query->all()));
    }
}
