<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Service;

use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\Model\ServiceInterface;
use KejawenLab\Application\Domain\ServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="SERVICE", actions={Permission::DELETE})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Delete extends AbstractController
{
    public function __construct(private ServiceService $service)
    {
    }

    /**
     * @Route("/services/{id}/delete", name=Delete::class, methods={"GET"})
     */
    public function __invoke(string $id): Response
    {
        $service = $this->service->get($id);
        if (!$service instanceof ServiceInterface) {
            $this->addFlash('error', 'sas.page.service.not_found');

            return new RedirectResponse($this->generateUrl(Main::class));
        }

        $this->service->remove($service);

        $this->addFlash('info', 'sas.page.service.deleted');

        return new RedirectResponse($this->generateUrl(Main::class));
    }
}
